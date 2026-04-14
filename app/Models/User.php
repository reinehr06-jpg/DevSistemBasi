<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasPermission($permission);
    }

    public function hasPermissionTo(string $module, string $action): bool
    {
        return $this->hasPermission("{$module}.{$action}");
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isDeveloper(): bool
    {
        return in_array($this->role?->slug, ['admin', 'developer']);
    }
}