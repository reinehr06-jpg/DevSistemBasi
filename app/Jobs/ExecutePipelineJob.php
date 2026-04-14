<?php

namespace App\Jobs;

use App\Models\PipelineRun;
use App\Services\PipelineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecutePipelineJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PipelineRun $run
    ) {}

    public function handle(PipelineService $service): void
    {
        $service->runFullPipeline($this->run);
    }
}