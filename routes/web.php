<?php

use App\Http\Controllers\BugController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\DevTaskController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WorkLogController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SystemProfileController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\AIOrchestratorController;
use App\Http\Controllers\AIWatcherController;
use App\Http\Controllers\AIActionsController;
use App\Http\Controllers\AIPredictionsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\EasyPanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard', [DashboardController::class, 'api'])->name('dashboard.api');
    Route::get('/api/servers', [DashboardController::class, 'apiServers'])->name('servers.api');
    
    // Systems
    Route::get('/systems', [SystemController::class, 'index'])->name('systems.index');
    Route::get('/systems/{system}', [SystemController::class, 'show'])->name('systems.show');
    Route::post('/systems', [SystemController::class, 'store'])->name('systems.store');
    Route::put('/systems/{system}', [SystemController::class, 'update'])->name('systems.update');
    Route::delete('/systems/{system}', [SystemController::class, 'destroy'])->name('systems.destroy');
    Route::post('/systems/{system}/detect', [SystemProfileController::class, 'detectFromSystem'])->name('systems.detect');
    
    Route::get('/dev-tasks', [DevTaskController::class, 'index'])->name('dev-tasks.index');
    Route::get('/dev-tasks/create', [DevTaskController::class, 'create'])->name('dev-tasks.create');
    Route::post('/dev-tasks', [DevTaskController::class, 'store'])->name('dev-tasks.store');
    Route::get('/dev-tasks/{devTask}', [DevTaskController::class, 'show'])->name('dev-tasks.show');
    Route::get('/dev-tasks/{devTask}/edit', [DevTaskController::class, 'edit'])->name('dev-tasks.edit');
    Route::put('/dev-tasks/{devTask}', [DevTaskController::class, 'update'])->name('dev-tasks.update');
    Route::delete('/dev-tasks/{devTask}', [DevTaskController::class, 'destroy'])->name('dev-tasks.destroy');

    Route::get('/bugs', [BugController::class, 'index'])->name('bugs.index');
    Route::get('/bugs/create', [BugController::class, 'create'])->name('bugs.create');
    Route::post('/bugs', [BugController::class, 'store'])->name('bugs.store');
    Route::get('/bugs/{bug}', [BugController::class, 'show'])->name('bugs.show');
    Route::get('/bugs/{bug}/edit', [BugController::class, 'edit'])->name('bugs.edit');
    Route::put('/bugs/{bug}', [BugController::class, 'update'])->name('bugs.update');
    Route::delete('/bugs/{bug}', [BugController::class, 'destroy'])->name('bugs.destroy');

    Route::post('/work-logs', [WorkLogController::class, 'store'])->name('work-logs.store');
    Route::put('/work-logs/{workLog}', [WorkLogController::class, 'update'])->name('work-logs.update');
    Route::get('/work-logs/active', [WorkLogController::class, 'active'])->name('work-logs.active');

    Route::get('/deploy', [DeployController::class, 'index'])->name('deploy.index');
    Route::post('/deploy/{server}', [DeployController::class, 'deploy'])->name('deploy.execute');
    Route::get('/deploy/{server}/status', [DeployController::class, 'status'])->name('deploy.status');

    Route::get('/servers', [ServerController::class, 'index'])->name('servers.index');
    Route::get('/servers/system/{systemId}', [ServerController::class, 'bySystem'])->name('servers.by-system');
    Route::get('/servers/{server}', [ServerController::class, 'show'])->name('servers.show');
    Route::post('/servers', [ServerController::class, 'store'])->name('servers.store');
    Route::put('/servers/{server}', [ServerController::class, 'update'])->name('servers.update');
    Route::delete('/servers/{server}', [ServerController::class, 'destroy'])->name('servers.destroy');
    Route::post('/servers/{server}/backup', [ServerController::class, 'manualBackup'])->name('servers.backup');

    Route::get('/integrations', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::get('/integrations/system/{systemId}', [IntegrationController::class, 'bySystem'])->name('integrations.by-system');
    Route::post('/integrations', [IntegrationController::class, 'store'])->name('integrations.store');
    Route::put('/integrations/{integration}', [IntegrationController::class, 'update'])->name('integrations.update');
    Route::delete('/integrations/{integration}', [IntegrationController::class, 'destroy'])->name('integrations.test');
    Route::post('/integrations/{integration}/test', [IntegrationController::class, 'test'])->name('integrations.test');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Workflows
    Route::get('/workflows', [WorkflowController::class, 'index'])->name('workflows.index');
    Route::post('/workflows', [WorkflowController::class, 'store'])->name('workflows.store');
    Route::put('/workflows/{workflow}', [WorkflowController::class, 'update'])->name('workflows.update');
    Route::delete('/workflows/{workflow}', [WorkflowController::class, 'destroy'])->name('workflows.destroy');
    Route::post('/workflows/{workflow}/toggle', [WorkflowController::class, 'toggle'])->name('workflows.toggle');

    // Alertas
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/rules', [AlertController::class, 'rules'])->name('alerts.rules');
    Route::post('/alerts/rules', [AlertController::class, 'storeRule'])->name('alerts.rules.store');
    Route::put('/alerts/rules/{rule}', [AlertController::class, 'updateRule'])->name('alerts.rules.update');
    Route::delete('/alerts/rules/{rule}', [AlertController::class, 'destroyRule'])->name('alerts.rules.destroy');
    Route::post('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');

    // System Profiles
    Route::get('/system-profiles', [SystemProfileController::class, 'index'])->name('system-profiles.index');
    Route::post('/system-profiles', [SystemProfileController::class, 'store'])->name('system-profiles.store');
    Route::put('/system-profiles/{profile}', [SystemProfileController::class, 'update'])->name('system-profiles.update');
    Route::delete('/system-profiles/{profile}', [SystemProfileController::class, 'destroy'])->name('system-profiles.destroy');
    Route::post('/system-profiles/detect', [SystemProfileController::class, 'detect'])->name('system-profiles.detect');
    Route::post('/systems/{system}/detect', [SystemProfileController::class, 'detectFromSystem'])->name('systems.detect');

    // Dependencies
    Route::get('/dependencies', [DependencyController::class, 'index'])->name('dependencies.index');
    Route::post('/dependencies', [DependencyController::class, 'store'])->name('dependencies.store');
    Route::put('/dependencies/{dependency}', [DependencyController::class, 'update'])->name('dependencies.update');
    Route::delete('/dependencies/{dependency}', [DependencyController::class, 'destroy'])->name('dependencies.destroy');

    // AI Orchestrator
    Route::get('/ai-orchestrator', [AIOrchestratorController::class, 'index'])->name('ai-orchestrator.index');
    Route::get('/ai-orchestrator/agents', [AIOrchestratorController::class, 'agents'])->name('ai-orchestrator.agents');
    Route::post('/ai-orchestrator/agents', [AIOrchestratorController::class, 'storeAgent'])->name('ai-orchestrator.agents.store');
    Route::put('/ai-orchestrator/agents/{agent}', [AIOrchestratorController::class, 'updateAgent'])->name('ai-orchestrator.agents.update');
    Route::delete('/ai-orchestrator/agents/{agent}', [AIOrchestratorController::class, 'destroyAgent'])->name('ai-orchestrator.agents.destroy');
    Route::get('/ai-orchestrator/flows', [AIOrchestratorController::class, 'flows'])->name('ai-orchestrator.flows');
    Route::post('/ai-orchestrator/flows', [AIOrchestratorController::class, 'storeFlow'])->name('ai-orchestrator.flows.store');
    Route::put('/ai-orchestrator/flows/{flow}', [AIOrchestratorController::class, 'updateFlow'])->name('ai-orchestrator.flows.update');
    Route::delete('/ai-orchestrator/flows/{flow}', [AIOrchestratorController::class, 'destroyFlow'])->name('ai-orchestrator.flows.destroy');
    Route::post('/ai-orchestrator/flows/{flow}/run', [AIOrchestratorController::class, 'runFlow'])->name('ai-orchestrator.flows.run');
    Route::get('/ai-orchestrator/executions', [AIOrchestratorController::class, 'executions'])->name('ai-orchestrator.executions');
    Route::get('/ai-orchestrator/executions/{execution}', [AIOrchestratorController::class, 'executionDetail'])->name('ai-orchestrator.executions.detail');
    Route::get('/ai-orchestrator/config', [AIOrchestratorController::class, 'config'])->name('ai-orchestrator.config');

    // AI Watcher
    Route::get('/ai-watcher', [AIWatcherController::class, 'index'])->name('ai-watcher.index');
    Route::get('/ai-watcher/config', [AIWatcherController::class, 'config'])->name('ai-watcher.config');
    Route::post('/ai-watcher/config', [AIWatcherController::class, 'storeConfig'])->name('ai-watcher.store');
    Route::get('/ai-watcher/logs', [AIWatcherController::class, 'logs'])->name('ai-watcher.logs');

    // AI Previsões
    Route::get('/ai-predictions', [AIPredictionsController::class, 'index'])->name('ai-predictions.index');
    Route::get('/ai-predictions/config', [AIPredictionsController::class, 'config'])->name('ai-predictions.config');

    // AI Ações Automáticas
    Route::get('/ai-actions', [AIActionsController::class, 'index'])->name('ai-actions.index');
    Route::get('/ai-actions/create', [AIActionsController::class, 'create'])->name('ai-actions.create');
    Route::post('/ai-actions', [AIActionsController::class, 'store'])->name('ai-actions.store');
    Route::get('/ai-actions/logs', [AIActionsController::class, 'logs'])->name('ai-actions.logs');

    // Developers
    Route::get('/developers', [DeveloperController::class, 'index'])->name('developers.index');
    Route::get('/developers/create', [DeveloperController::class, 'create'])->name('developers.create');
    Route::post('/developers', [DeveloperController::class, 'store'])->name('developers.store');
    Route::get('/developers/{developer}', [DeveloperController::class, 'show'])->name('developers.show');
    Route::get('/developers/{developer}/edit', [DeveloperController::class, 'edit'])->name('developers.edit');
    Route::put('/developers/{developer}', [DeveloperController::class, 'update'])->name('developers.update');
    Route::delete('/developers/{developer}', [DeveloperController::class, 'destroy'])->name('developers.destroy');

    // Performance & Ranking
    Route::get('/developers/performance', [DeveloperController::class, 'performance'])->name('developers.performance');
    Route::get('/developers/ranking', [DeveloperController::class, 'ranking'])->name('developers.ranking');

    // Teams
    Route::get('/developers/teams', [DeveloperController::class, 'teams'])->name('developers.teams');
    Route::post('/developers/teams', [DeveloperController::class, 'storeTeam'])->name('developers.teams.store');
    Route::put('/developers/teams/{team}', [DeveloperController::class, 'updateTeam'])->name('developers.teams.update');
    Route::delete('/developers/teams/{team}', [DeveloperController::class, 'destroyTeam'])->name('developers.teams.destroy');

    // Backups
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::get('/backups/status', [BackupController::class, 'status'])->name('backups.status');
    Route::get('/backups/systems', [BackupController::class, 'systems'])->name('backups.systems');
    Route::get('/backups/destinations', [BackupController::class, 'destinations'])->name('backups.destinations');
    Route::post('/backups/destinations', [BackupController::class, 'storeDestination'])->name('backups.destinations.store');
    Route::put('/backups/destinations/{destination}', [BackupController::class, 'updateDestination'])->name('backups.destinations.update');
    Route::delete('/backups/destinations/{destination}', [BackupController::class, 'destroyDestination'])->name('backups.destinations.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'api'])->name('notifications.api');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');

    // Pipelines
    Route::get('/pipelines', [PipelineController::class, 'index'])->name('pipelines.index');
    Route::get('/pipelines/create', [PipelineController::class, 'create'])->name('pipelines.create');
    Route::get('/pipelines/{pipeline}', [PipelineController::class, 'show'])->name('pipelines.show');
    Route::post('/pipelines', [PipelineController::class, 'store'])->name('pipelines.store');
    Route::put('/pipelines/{pipeline}', [PipelineController::class, 'update'])->name('pipelines.update');
    Route::delete('/pipelines/{pipeline}', [PipelineController::class, 'destroy'])->name('pipelines.destroy');
    Route::post('/pipelines/{pipeline}/run', [PipelineController::class, 'run'])->name('pipelines.run');
    Route::get('/pipelines/{pipeline}/runs', [PipelineController::class, 'runs'])->name('pipelines.runs');
    Route::get('/pipelines/runs/{run}', [PipelineController::class, 'runDetail'])->name('pipelines.run-detail');
    Route::post('/pipelines/runs/{run}/cancel', [PipelineController::class, 'cancel'])->name('pipelines.cancel');
    Route::post('/pipelines/runs/{run}/rollback', [PipelineController::class, 'rollback'])->name('pipelines.rollback');

    // EasyPanel
    Route::get('/easypanel/servers', [EasyPanelController::class, 'servers'])->name('easypanel.servers');
    Route::get('/easypanel/servers/{serverId}/metrics', [EasyPanelController::class, 'metrics'])->name('easypanel.metrics');
    Route::get('/easypanel/servers/{serverId}/logs', [EasyPanelController::class, 'logs'])->name('easypanel.logs');
    Route::get('/easypanel/servers/{serverId}/logs/stream', [EasyPanelController::class, 'logsStream'])->name('easypanel.logs-stream');
    Route::get('/easypanel/servers/{serverId}/status', [EasyPanelController::class, 'status'])->name('easypanel.status');
    Route::post('/easypanel/servers/{serverId}/deploy', [EasyPanelController::class, 'deploy'])->name('easypanel.deploy');
    Route::post('/easypanel/servers/{serverId}/restart', [EasyPanelController::class, 'restart'])->name('easypanel.restart');
    Route::get('/easypanel/servers/{serverId}/services', [EasyPanelController::class, 'services'])->name('easypanel.services');
    Route::post('/easypanel/servers/{serverId}/exec', [EasyPanelController::class, 'exec'])->name('easypanel.exec');
});

Route::post('/webhook/bitbucket', [WebhookController::class, 'handleBitbucket'])->name('webhook.bitbucket');
Route::post('/webhook', [WebhookController::class, 'handle'])->name('webhook.handle');

Route::post('/api/server/update', [ServerController::class, 'apiUpdate']);

// API EasyPanel
Route::get('/api/easypanel/servers', [EasyPanelController::class, 'apiServers']);
Route::post('/api/easypanel/servers/sync', [EasyPanelController::class, 'syncServers']);

// API Pipeline
Route::get('/api/pipelines', [PipelineController::class, 'api']);
Route::post('/api/pipelines/{pipeline}/run', [PipelineController::class, 'apiRun']);

require __DIR__.'/auth.php';