<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerDependency extends Model
{
    protected $fillable = [
        'server_id',
        'depends_on_server_id',
        'depends_on_type',
        'depends_on_name',
        'depends_on_host',
        'connection_type',
        'status',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function dependsOnServer(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'depends_on_server_id');
    }

    public static function getAvailableTypes(): array
    {
        return ['database', 'cache', 'queue', 'api', 'external', 'filesystem'];
    }

    public static function getAvailableConnectionTypes(): array
    {
        return ['tcp', 'http', 'ssh', 'unix', 'redis', 'amqp'];
    }
}