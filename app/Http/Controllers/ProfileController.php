<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Actualizar perfil del usuario
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Permite que el email actual sea igual, pero otros emails deben ser únicos
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'Ingresa un email válido',
            'email.unique' => 'Este email ya está registrado',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres',
            'bio.max' => 'La biografía no puede exceder 500 caracteres',
            'avatar.image' => 'El archivo debe ser una imagen',
            'avatar.mimes' => 'La imagen debe ser: jpeg, png, jpg o gif',
            'avatar.max' => 'La imagen no puede exceder 2MB',
        ]);

        // Actualizar datos básicos
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        // Procesar avatar si se carga uno
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            // Guardar nuevo avatar
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'uploads/avatars/' . $filename;

            // Crear carpeta si no existe
            if (!file_exists(public_path('uploads/avatars'))) {
                mkdir(public_path('uploads/avatars'), 0777, true);
            }

            $file->move(public_path('uploads/avatars'), $filename);
            $user->update(['avatar' => $path]);
        }

        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Mostrar perfil público del usuario
     */
    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('profile.show', compact('user'));
    }
}
