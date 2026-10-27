<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Laboratorio;
use App\Models\Rol;
use App\Models\User;
use App\Models\UsuarioLaboratorio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Buscar un usuario por su matrícula/credencial institucional o por su correo electrónico.
     */
    public function buscarPorIdentificador(string $identificador): ?User
    {
        $idLimpio = trim($identificador);

        return User::with('roles')
            ->where('matricula', $idLimpio)
            ->orWhere('email', $idLimpio)
            ->first();
    }

    /**
     * Validar credenciales de acceso por matrícula/credencial o email.
     */
    public function autenticar(string $identificador, string $password): ?User
    {
        $usuario = $this->buscarPorIdentificador($identificador);

        if (!$usuario) {
            return null;
        }

        if (!Hash::check($password, $usuario->password)) {
            $this->bitacoraService->registrar(
                accion: 'Intento Fallido de Inicio de Sesión',
                modulo: 'Autenticación',
                detalle: "Intento fallido para el identificador: {$identificador}"
            );
            return null;
        }

        if (!$usuario->isActive()) {
            throw new BusinessRuleException(
                "La cuenta de usuario se encuentra inactiva. Comuníquese con la administración.",
                'USUARIO_INACTIVO'
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Inicio de Sesión Exitoso',
            modulo: 'Autenticación',
            detalle: "Sesión iniciada por {$usuario->name} [Matrícula: {$usuario->matricula}]",
            userId: $usuario->id
        );

        return $usuario;
    }

    /**
     * Registrar entrada o salida física de un laboratorio mediante escaneo de credencial o matrícula.
     */
    public function registrarAccesoLaboratorio(
        string $identificador,
        int $laboratorioId,
        string $tipoMovimiento = 'entrada'
    ): array {
        $usuario = $this->buscarPorIdentificador($identificador);

        if (!$usuario) {
            throw new BusinessRuleException(
                "No se encontró ningún usuario registrado con la matrícula o credencial '{$identificador}'.",
                'USUARIO_NO_ENCONTRADO'
            );
        }

        if (!$usuario->isActive()) {
            throw new BusinessRuleException(
                "El usuario {$usuario->name} se encuentra inactivo en el sistema.",
                'USUARIO_INACTIVO'
            );
        }

        $laboratorio = Laboratorio::findOrFail($laboratorioId);
        if (!$laboratorio->activo) {
            throw new BusinessRuleException(
                "El laboratorio '{$laboratorio->nombre}' se encuentra inactivo.",
                'LABORATORIO_INACTIVO'
            );
        }

        $ahora = Carbon::now();

        // Vincular en usuario_laboratorio si no existía relación previa
        $usuarioLab = UsuarioLaboratorio::firstOrCreate(
            [
                'user_id' => $usuario->id,
                'laboratorio_id' => $laboratorio->id,
            ],
            [
                'tipo_acceso' => 'consulta',
            ]
        );

        // Registrar en bitácora de accesos
        $this->bitacoraService->registrar(
            accion: 'Control de Acceso: ' . ucfirst($tipoMovimiento),
            modulo: 'Control de Acceso',
            detalle: "Acceso ({$tipoMovimiento}) registrado para {$usuario->name} [Matrícula: {$usuario->matricula}] en '{$laboratorio->nombre}' a las {$ahora->format('H:i:s')}",
            userId: $usuario->id
        );

        return [
            'usuario' => $usuario,
            'laboratorio' => $laboratorio,
            'tipo' => $tipoMovimiento,
            'hora' => $ahora->toIso8601String(),
        ];
    }

    /**
     * Crear un nuevo usuario en el sistema y asignarle rol.
     */
    public function crearUsuario(array $datos, ?string $nombreRol = null, ?int $creadoPor = null): User
    {
        $email = trim(strtolower($datos['email']));

        if (User::where('email', $email)->exists()) {
            throw new BusinessRuleException(
                "El correo electrónico '{$email}' ya se encuentra registrado.",
                'EMAIL_DUPLICADO'
            );
        }

        if (!empty($datos['matricula'])) {
            $matricula = trim($datos['matricula']);
            if (User::where('matricula', $matricula)->exists()) {
                throw new BusinessRuleException(
                    "La matrícula o credencial '{$matricula}' ya está registrada.",
                    'MATRICULA_DUPLICADA'
                );
            }
        }

        $usuario = User::create([
            'name' => trim($datos['name']),
            'email' => $email,
            'matricula' => $datos['matricula'] ?? null,
            'password' => Hash::make($datos['password']),
            'activo' => isset($datos['activo']) ? (bool) $datos['activo'] : true,
            'telefono' => $datos['telefono'] ?? null,
        ]);

        if ($nombreRol) {
            $usuario->assignRole($nombreRol);
        }

        $this->bitacoraService->registrar(
            accion: 'Crear Usuario',
            modulo: 'Usuarios y Roles',
            detalle: "Usuario creado: {$usuario->name} ({$usuario->email}) con rol: " . ($nombreRol ?? 'Sin rol'),
            userId: $creadoPor
        );

        return $usuario;
    }

    /**
     * Desactivar lógicamente un usuario conservando su historial.
     */
    public function desactivarUsuario(User $usuario, string $motivo, ?int $adminId = null): bool
    {
        $usuario->activo = false;
        $usuario->save();

        $this->bitacoraService->registrar(
            accion: 'Desactivar Usuario',
            modulo: 'Usuarios y Roles',
            detalle: "Usuario ID {$usuario->id} ({$usuario->name}) desactivado lógicamente. Motivo: {$motivo}",
            userId: $adminId
        );

        return true;
    }

    /**
     * Reactivar un usuario.
     */
    public function activarUsuario(User $usuario, ?int $adminId = null): bool
    {
        $usuario->activo = true;
        $usuario->save();

        $this->bitacoraService->registrar(
            accion: 'Activar Usuario',
            modulo: 'Usuarios y Roles',
            detalle: "Usuario ID {$usuario->id} ({$usuario->name}) reactivado.",
            userId: $adminId
        );

        return true;
    }
}
