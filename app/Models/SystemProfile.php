<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemProfile extends Model
{
    protected $fillable = [
        'system_id',
        'language',
        'framework',
        'php_version',
        'node_version',
        'database_type',
        'database_version',
        'dependencies',
        'integrations',
        'repository_url',
        'documentation_url',
        'description',
    ];

    protected $casts = [
        'dependencies' => 'array',
        'integrations' => 'array',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public static function getAvailableLanguages(): array
    {
        return ['PHP', 'Python', 'Node.js', 'Java', 'Go', 'Ruby', 'C#', 'Other'];
    }

    public static function getAvailableFrameworks(): array
    {
        return [
            'PHP' => ['Laravel', 'Symfony', 'CodeIgniter', 'CakePHP', 'Yii'],
            'Python' => ['Django', 'Flask', 'FastAPI', 'Pyramid'],
            'Node.js' => ['Express', 'NestJS', 'Fastify', 'Koa'],
            'Java' => ['Spring', 'Jakarta EE', 'Quarkus'],
            'Go' => ['Gin', 'Echo', 'Fiber', 'Chi'],
            'Ruby' => ['Rails', 'Sinatra'],
            'C#' => ['ASP.NET Core', 'Nancy'],
        ];
    }

    public static function getAvailableDatabases(): array
    {
        return ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'MariaDB', 'Oracle', 'SQL Server'];
    }
}