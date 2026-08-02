<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cedula_profesional' => 'nullable|string|max:50',
            'especialidad' => 'nullable|string|max:255',
            'cedula_especialidad' => 'nullable|string|max:500',
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

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'cedula_profesional' => $request->cedula_profesional,
            'especialidad' => $request->especialidad,
            'cedula_especialidad' => $request->cedula_especialidad,
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'uploads/avatars/' . $filename;

            if (!file_exists(public_path('uploads/avatars'))) {
                mkdir(public_path('uploads/avatars'), 0777, true);
            }

            $file->move(public_path('uploads/avatars'), $filename);
            $user->update(['avatar' => $path]);
        }

        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente');
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('profile.show', compact('user'));
    }
}
