<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasPermissionTo(string $module, string $action): bool
    {
        $permission = "{$module}.{$action}";
        return $this->hasPermission($permission);
    }

    public static function getDefaultRoles(): array
    {
        return [
            'admin' => [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acesso completo ao sistema',
                'permissions' => [
                    'systems.view', 'systems.create', 'systems.edit', 'systems.delete',
                    'servers.view', 'servers.create', 'servers.edit', 'servers.delete',
                    'bugs.view', 'bugs.create', 'bugs.edit', 'bugs.delete',
                    'dev-tasks.view', 'dev-tasks.create', 'dev-tasks.edit', 'dev-tasks.delete',
                    'deploys.view', 'deploys.create', 'deploys.execute',
                    'ai.view', 'ai.create', 'ai.edit', 'ai.execute',
                    'backups.view', 'backups.create', 'backups.execute',
                    'alerts.view', 'alerts.create', 'alerts.edit', 'alerts.resolve',
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'settings.view', 'settings.edit',
                ],
            ],
            'developer' => [
                'name' => 'Desenvolvedor',
                'slug' => 'developer',
                'description' => 'Acesso a desenvolvimento e deploys',
                'permissions' => [
                    'systems.view', 'servers.view',
                    'bugs.view', 'bugs.create', 'bugs.edit',
                    'dev-tasks.view', 'dev-tasks.create', 'dev-tasks.edit',
                    'deploys.view', 'deploys.create', 'deploys.execute',
                    'ai.view', 'ai.create', 'ai.execute',
                    'backups.view', 'backups.execute',
                    'alerts.view',
                ],
            ],
            'viewer' => [
                'name' => 'Visualizador',
                'slug' => 'viewer',
                'description' => 'Acesso apenas para visualização',
                'permissions' => [
                    'systems.view',
                    'servers.view',
                    'bugs.view',
                    'dev-tasks.view',
                    'deploys.view',
                    'ai.view',
                    'backups.view',
                    'alerts.view',
                ],
            ],
        ];
    }
}