<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_RECEPTIONIST = 'recepcionista';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'bio', 'avatar',
        'cedula_profesional', 'especialidad', 'cedula_especialidad',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'permissions'       => 'array',
    ];

    public function appointments() { return $this->hasMany(Appointment::class); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }
    public function creator() { return $this->belongsTo(self::class, 'created_by'); }
    public function activityLogs() { return $this->hasMany(ActivityLog::class); }

    public function isDoctor(): bool { return $this->role === self::ROLE_DOCTOR; }
    public function isReceptionist(): bool { return $this->role === self::ROLE_RECEPTIONIST; }

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->isDoctor()) {
            return true; // el doctor siempre tiene todo; nada de lo guardado lo reduce
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    public static function allPermissionKeys(): array
    {
        return collect(config('permissions.catalog'))
            ->flatMap(fn ($group) => array_keys($group))
            ->values()->all();
    }

    public static function sanitizePermissions(array $permissions): array
    {
        return array_values(array_intersect($permissions, self::allPermissionKeys()));
    }

    /** Doctor que firma documentos emitidos por la recepción. */
    public static function firmante(): ?self
    {
        return static::where('role', self::ROLE_DOCTOR)->where('is_active', true)->orderBy('id')->first();
    }
}
