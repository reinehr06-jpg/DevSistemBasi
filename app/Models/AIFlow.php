<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIFlow extends Model
{
    protected $fillable = [
        'name',
        'trigger',
        'system_id',
        'is_active',
        'timeout_minutes',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'timeout_minutes' => 'integer',
        'config' => 'array',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(AIFlowStep::class, 'flow_id')->orderBy('step_order');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(AIExecution::class, 'flow_id');
    }

    public static function getAvailableTriggers(): array
    {
        return [
            'error_detected' => 'Erro Detectado',
            'alert_triggered' => 'Alerta Disparado',
            'deploy_completed' => 'Deploy Completo',
            'deploy_failed' => 'Deploy Falhou',
            'backup_completed' => 'Backup Completo',
            'backup_failed' => 'Backup Falhou',
            'server_offline' => 'Servidor Offline',
            'server_online' => 'Servidor Online',
            'metrics_received' => 'Métricas Recebidas',
            'webhook_received' => 'Webhook Recebido',
            'task_completed' => 'Tarefa Completa',
            'manual' => 'Execução Manual',
        ];
    }

    public function getTotalExecutionsAttribute(): int
    {
        return $this->executions()->count();
    }

    public function getSuccessRateAttribute(): float
    {
        $total = $this->executions()->count();
        if ($total === 0) return 0;
        
        $success = $this->executions()->where('status', 'completed')->count();
        return round(($success / $total) * 100, 1);
    }
}