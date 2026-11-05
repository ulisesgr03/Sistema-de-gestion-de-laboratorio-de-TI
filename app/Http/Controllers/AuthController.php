<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión institucional.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('panel.operativo');
        }

        $laboratorios = Laboratorio::where('activo', true)->orderBy('nombre')->get();

        return view('auth.login', compact('laboratorios'));
    }

    /**
     * Procesa la autenticación institucional mediante Matrícula o Correo Institucional.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
            'laboratory' => ['nullable', 'string'],
            'remember' => ['nullable'],
        ], [
            'identifier.required' => 'Debe ingresar su matrícula institucional o clave de empleado.',
            'password.required' => 'Debe ingresar su contraseña de dominio Active Directory / LDAP.',
        ]);

        $identifier = trim($credentials['identifier']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Intentar autenticación por matrícula o por correo electrónico
        $attemptMatricula = Auth::attempt(['matricula' => $identifier, 'password' => $password], $remember);
        $attemptEmail = false;

        if (!$attemptMatricula) {
            $attemptEmail = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
        }

        if ($attemptMatricula || $attemptEmail) {
            /** @var User $user */
            $user = Auth::user();

            // Verificar si el usuario está activo en la institución
            if (isset($user->activo) && !$user->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('identifier', 'laboratory'))
                    ->withErrors([
                        'identifier' => 'Su cuenta institucional se encuentra inactiva. Favor de acudir a Servicios Escolares o Jefatura de TI.',
                    ]);
            }

            $request->session()->regenerate();

            // Guardar contexto de laboratorio concurrente si fue seleccionado
            if (!empty($credentials['laboratory']) && $credentials['laboratory'] !== 'all') {
                session(['concurrente_lab' => $credentials['laboratory']]);
            }

            return redirect()->intended(route('panel.operativo'))->with(
                'success',
                'Sesión iniciada correctamente. Bienvenido, ' . $user->name . '.'
            );
        }

        return back()
            ->withInput($request->only('identifier', 'laboratory', 'remember'))
            ->withErrors([
                'identifier' => 'Las credenciales ingresadas no coinciden con los registros institucionales de Active Directory / LDAP.',
            ]);
    }

    /**
     * Cierra la sesión activa del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Ha cerrado su sesión institucional correctamente.');
    }
}
