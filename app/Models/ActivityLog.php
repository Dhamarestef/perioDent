<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'description', 'subject_type', 'subject_id', 'ip_address',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(?User $user, string $action, ?string $description = null, ?string $type = null, ?int $id = null): void
    {
        if (! $user) {
            return;
        }

        try {
            static::create([
                'user_id'      => $user->id,
                'action'       => $action,
                'description'  => $description ?? config("permissions.activity_labels.$action", $action),
                'subject_type' => $type,
                'subject_id'   => $id,
                'ip_address'   => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e); // la bitácora nunca debe romper la operación
        }
    }
}
