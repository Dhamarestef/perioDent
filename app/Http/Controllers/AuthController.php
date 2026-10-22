<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        abort_if(User::exists(), 404); // solo para crear al primer doctor
        return view('auth.register');
    }

    /**
     * Procesar registro de nuevo usuario
     */
    public function register(Request $request)
    {
        abort_if(User::exists(), 404);
        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Autenticar al usuario automáticamente
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '¡Bienvenido a nuestra plataforma!');
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $existing = User::where('email', $request->email)->first();
        if ($existing && ! $existing->is_active && Hash::check($request->password, $existing->password)) {
            return back()->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al doctor.'])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            ActivityLog::record(Auth::user()->isReceptionist() ? Auth::user() : null, 'login');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        if (Auth::user()?->isReceptionist()) {
            ActivityLog::record(Auth::user(), 'logout');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    }
}
