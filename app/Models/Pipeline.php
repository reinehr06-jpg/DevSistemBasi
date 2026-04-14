<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PipelineRun;

class Pipeline extends Model
{
    protected $fillable = [
        'system_id',
        'name',
        'description',
        'stages',
        'auto_deploy',
        'ia_approval',
        'repository_url',
        'deploy_branch',
        'ia_agent',
        'active',
    ];

    protected $casts = [
        'stages' => 'array',
        'auto_deploy' => 'boolean',
        'ia_approval' => 'boolean',
        'active' => 'boolean',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(PipelineRun::class);
    }

    public function latestRun(): HasMany
    {
        return $this->hasMany(PipelineRun::class)->latest()->limit(1);
    }

    public static function getDefaultStages(): array
    {
        return [
            'git:fetch',
            'lint',
            'test',
            'ia:analyze',
            'deploy:dev',
            'deploy:staging',
            'health',
            'deploy:prod',
        ];
    }

    public function getStagesAttribute($value): array
    {
        return $value ?? self::getDefaultStages();
    }
}