<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    private function receptionist($id): User
    {
        return User::where('role', User::ROLE_RECEPTIONIST)->findOrFail($id);
    }

    public function index()
    {
        $receptionists = User::where('role', User::ROLE_RECEPTIONIST)->orderBy('name')->get();

        return view('users.index', compact('receptionists'));
    }

    public function create()
    {
        return view('users.form', [
            'user'     => null,
            'catalog'  => config('permissions.catalog'),
            'selected' => config('permissions.receptionist_defaults'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|string|min:8|confirmed',
            'permissions'   => 'nullable|array',
            'permissions.*' => ['nullable', Rule::in(User::allPermissionKeys())],
        ], $this->messages());

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null]);
        $user->password = $data['password']; // el cast 'hashed' lo encripta
        $user->forceFill([
            'role'        => User::ROLE_RECEPTIONIST,
            'is_active'   => true,
            'permissions' => User::sanitizePermissions($data['permissions'] ?? []),
            'created_by'  => auth()->id(),
        ])->save();

        return redirect()->route('users.index')->with('success', 'Recepcionista creada correctamente');
    }

    public function edit($id)
    {
        $user = $this->receptionist($id);

        return view('users.form', [
            'user'     => $user,
            'catalog'  => config('permissions.catalog'),
            'selected' => $user->permissions ?? [],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $this->receptionist($id);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'         => 'nullable|string|max:20',
            'permissions'   => 'nullable|array',
            'permissions.*' => ['nullable', Rule::in(User::allPermissionKeys())],
        ], $this->messages());

        $user->fill(['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null]);
        $user->forceFill(['permissions' => User::sanitizePermissions($data['permissions'] ?? [])])->save();

        return redirect()->route('users.index')->with('success', 'Recepcionista actualizada correctamente');
    }

    public function toggle($id)
    {
        $user = $this->receptionist($id);
        $user->forceFill(['is_active' => ! $user->is_active, 'remember_token' => null])->save();

        if (! $user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete(); // cierra sus sesiones abiertas
        }

        return back()->with('success', $user->is_active ? 'Cuenta activada' : 'Cuenta desactivada');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = $this->receptionist($id);

        $request->validate(['password' => 'required|string|min:8|confirmed'], $this->messages());

        $user->password = $request->password;
        $user->remember_token = null;
        $user->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Contraseña restablecida');
    }

    public function activity(Request $request)
    {
        $receptionists = User::where('role', User::ROLE_RECEPTIONIST)->orderBy('name')->get(['id', 'name']);

        $logs = ActivityLog::with('user:id,name')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->hasta))
            ->latest('created_at')->latest('id')
            ->simplePaginate(30)->withQueryString();

        return view('users.activity', compact('logs', 'receptionists'));
    }

    private function messages(): array
    {
        return [
            'name.required'      => 'El nombre es requerido',
            'email.required'     => 'El correo es requerido',
            'email.unique'       => 'Este correo ya está registrado',
            'password.required'  => 'La contraseña es requerida',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}
