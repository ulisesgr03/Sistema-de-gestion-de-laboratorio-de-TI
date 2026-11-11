<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión institucional de MetalNet.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            return redirect()->to($user->dashboardRoute());
        }

        $laboratorios = Laboratorio::where('activo', true)->orderBy('id')->get();
        $avisoContexto = null;

        if ($request->query('motivo') === 'solicitar_software') {
            $swName = $request->query('software');
            $avisoContexto = $swName
                ? "Para solicitar la instalación o actualización de \"{$swName}\", inicia sesión con tu cuenta de Alumno o Docente (o regístrate)."
                : 'Debes iniciar sesión con tu cuenta institucional para enviar una solicitud de software o actualización al Administrador.';
        } elseif ($request->query('motivo') === 'reservar') {
            $avisoContexto = 'Inicia sesión con tu cuenta institucional para reservar estaciones o salas de laboratorio.';
        }

        return view('auth.login', compact('laboratorios', 'avisoContexto'));
    }

    /**
     * Muestra el formulario de registro de nuevas cuentas institucionales.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            return redirect()->to($user->dashboardRoute());
        }

        return view('auth.register');
    }

    /**
     * Procesa la autenticación mediante Correo Institucional o Matrícula.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ], [
            'identifier.required' => 'Ingrese su correo institucional (@metalnet.edu) o matrícula.',
            'password.required' => 'Ingrese su contraseña de acceso.',
        ]);

        $identifier = trim($credentials['identifier']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        $attemptEmail = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
        $attemptMatricula = false;

        if (!$attemptEmail) {
            $attemptMatricula = Auth::attempt(['matricula' => $identifier, 'password' => $password], $remember);
        }

        if ($attemptEmail || $attemptMatricula) {
            /** @var User $user */
            $user = Auth::user();

            if (isset($user->activo) && !$user->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('identifier'))
                    ->withErrors([
                        'identifier' => 'Su cuenta institucional se encuentra inactiva. Contacte al Administrador de MetalNet.',
                    ]);
            }

            $request->session()->regenerate();

            return redirect()->to($user->dashboardRoute())->with(
                'success',
                "Sesión autenticada en MetalNet. Bienvenido, {$user->name} ({$user->primaryRoleLabel()})."
            );
        }

        return back()
            ->withInput($request->only('identifier', 'remember'))
            ->withErrors([
                'identifier' => 'Las credenciales ingresadas no coinciden con los registros de MetalNet.',
            ]);
    }

    /**
     * Registra un nuevo usuario institucional (Alumno o Docente) y redirige a su panel privado.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'matricula' => ['required', 'string', 'max:50', 'unique:users,matricula'],
            'rol' => ['required', 'in:alumno,docente'],
            'telefono' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Este correo institucional ya se encuentra registrado.',
            'matricula.unique' => 'Esta matrícula o clave institucional ya está registrada.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'matricula' => strtoupper(trim($validated['matricula'])),
            'telefono' => $validated['telefono'] ?? null,
            'password' => Hash::make($validated['password']),
            'activo' => true,
        ]);

        $user->assignRole($validated['rol']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to($user->dashboardRoute())->with(
            'success',
            "Cuenta registrada exitosamente. Bienvenido a tu panel de {$user->primaryRoleLabel()}, {$user->name}."
        );
    }

    /**
     * Cierra la sesión activa del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Ha cerrado su sesión en MetalNet.');
    }
}
