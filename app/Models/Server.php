<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Server extends Model
{
    protected $fillable = [
        'system_id',
        'name',
        'ip',
        'ssh_user',
        'ssh_key_path',
        'deploy_path',
        'status',
        'cpu_usage',
        'ram_usage',
    ];

    protected $casts = [
        'cpu_usage' => 'float',
        'ram_usage' => 'float',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }
}