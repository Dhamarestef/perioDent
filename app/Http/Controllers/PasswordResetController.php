<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Mostrar formulario para solicitar reset
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar email con enlace de reset
     */
    public function sendResetLink(Request $request)
    {
        // Validar email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El email es requerido',
            'email.email' => 'Ingresa un email válido',
            'email.exists' => 'No encontramos una cuenta con este email',
        ]);

        // Buscar usuario
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email no encontrado']);
        }

        // Generar token único
        $token = Str::random(64);

        // Guardar token en BD
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Crear enlace de reset
        $resetLink = route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);

        // Enviar email
        Mail::send('emails.password-reset', [
            'user' => $user,
            'resetLink' => $resetLink,
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Recuperar Contraseña');
        });

        return back()->with('success', 'Hemos enviado un enlace para recuperar tu contraseña al email registrado');
    }

    /**
     * Mostrar formulario para ingresar nueva contraseña
     */
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        // Verificar que el token existe y es válido
        $reset = DB::table('password_resets')
                    ->where('email', $email)
                    ->first();

        if (!$reset) {
            return redirect()->route('login')->withErrors(['error' => 'Enlace de recuperación inválido']);
        }

        // Verificar que el token no ha expirado (1 hora)
        if ($reset->created_at < now()->subHour()) {
            DB::table('password_resets')->where('email', $email)->delete();
            return redirect()->route('login')->withErrors(['error' => 'El enlace de recuperación ha expirado']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Actualizar la contraseña
     */
    public function updatePassword(Request $request)
    {
        // Validar datos
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Verificar token
        $reset = DB::table('password_resets')
                    ->where('email', $request->email)
                    ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['error' => 'Token inválido']);
        }

        // Verificar expiración
        if ($reset->created_at < now()->subHour()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['error' => 'El enlace ha expirado']);
        }

        // Actualizar contraseña
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Eliminar el token usado
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente. Por favor inicia sesión');
    }
}
