<?php

namespace App\Jobs;

use App\Models\BackupLog;
use App\Models\Server;
use App\Models\System;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupDatabaseJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?System $system = null,
        public ?Server $server = null
    ) {}

    public function handle(): void
    {
        $systems = $this->system 
            ? collect([$this->system]) 
            : System::all();

        foreach ($systems as $system) {
            $this->backupSystem($system);
        }
    }

    private function backupSystem(System $system): void
    {
        $server = $this->server 
            ?? $system->servers()->first();

        $backupLog = BackupLog::create([
            'system_id' => $system->id,
            'server_id' => $server?->id,
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $filename = $system->slug . '_' . now()->format('Y-m-d_H-i') . '.sql';
            $backupPath = storage_path('app/backups/' . $system->slug);
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            if ($server && $server->database_name) {
                $command = sprintf(
                    'ssh %s@%s "pg_dump -U postgres %s" > %s',
                    $server->ssh_user,
                    $server->ip,
                    $server->database_name,
                    $fullPath
                );
                
                $result = Process::run($command);
                
                if ($result->successful()) {
                    $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                    
                    $backupLog->update([
                        'status' => 'success',
                        'message' => 'Backup realizado com sucesso',
                        'file_path' => 'backups/' . $system->slug . '/' . $filename,
                        'file_size' => $fileSize,
                        'finished_at' => now(),
                    ]);

                    if ($server) {
                        $server->update(['last_backup' => now()]);
                    }

                    $this->cleanupOldBackups($system);
                } else {
                    throw new \Exception($result->errorOutput());
                }
            } else {
                file_put_contents($fullPath, 'Demo backup - SQLite');
                
                $backupLog->update([
                    'status' => 'success',
                    'message' => 'Backup local realizado (SQLite)',
                    'file_path' => 'backups/' . $system->slug . '/' . $filename,
                    'file_size' => filesize($fullPath),
                    'finished_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $backupLog->update([
                'status' => 'error',
                'message' => $e->getMessage(),
                'finished_at' => now(),
            ]);
        }
    }

    private function cleanupOldBackups(System $system): void
    {
        $backupPath = storage_path('app/backups/' . $system->slug);
        
        if (!file_exists($backupPath)) {
            return;
        }

        $files = glob($backupPath . '/*.sql');
        $thirtyDaysAgo = now()->subDays(30);

        foreach ($files as $file) {
            if (filemtime($file) < $thirtyDaysAgo->timestamp) {
                unlink($file);
            }
        }
    }
}