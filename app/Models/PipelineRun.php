<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineRun extends Model
{
    protected $fillable = [
        'pipeline_id',
        'server_id',
        'user_id',
        'environment',
        'status',
        'branch',
        'commit_hash',
        'commit_message',
        'changes',
        'stage_index',
        'current_stage',
        'stages_result',
        'ia_analysis',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'stages_result' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'rejected']);
    }

    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function markAsSuccess(): void
    {
        $this->update([
            'status' => 'success',
            'finished_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'finished_at' => now(),
        ]);
    }

    public function updateStage(string $stage, array $result): void
    {
        $stagesResult = $this->stages_result ?? [];
        $stagesResult[$stage] = $result;
        
        $this->update([
            'current_stage' => $stage,
            'stages_result' => $stagesResult,
        ]);
    }

    public function setIaAnalysis(string $analysis, bool $approved): void
    {
        $this->update([
            'ia_analysis' => $analysis,
            'status' => $approved ? 'approved' : 'rejected',
        ]);
    }

    public static function getAvailableStatuses(): array
    {
        return [
            'pending', 'running', 'waiting_ia', 
            'approved', 'rejected', 'success', 
            'failed', 'cancelled', 'rollback'
        ];
    }

    public static function getAvailableEnvironments(): array
    {
        return ['dev', 'staging', 'production'];
    }
}