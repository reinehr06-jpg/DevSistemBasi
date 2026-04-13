<?php

use App\Http\Controllers\BugController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\DevTaskController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WorkLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
});

Route::post('/webhook/bitbucket', [WebhookController::class, 'handleBitbucket'])->name('webhook.bitbucket');
Route::post('/webhook', [WebhookController::class, 'handle'])->name('webhook.handle');

Route::post('/api/server/update', [ServerController::class, 'apiUpdate']);

require __DIR__.'/auth.php';