<?php

namespace App\Console\Commands;

use App\Models\BackupDestination;
use App\Models\BackupLog;
use App\Models\System;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupDatabasesCommand extends Command
{
    protected $signature = 'backup:databases';
    protected $description = 'Executa backup diário de todos os sistemas cadastrados';

    public function handle(): int
    {
        $systems = System::where('active', true)->get();
        
        if ($systems->isEmpty()) {
            $this->info('Nenhum sistema ativo encontrado.');
            return Command::SUCCESS;
        }

        $destinations = BackupDestination::where('active', true)->get();
        
        if ($destinations->isEmpty()) {
            $this->warn('Nenhum destino de backup configurado.');
            return Command::FAILURE;
        }

        foreach ($systems as $system) {
            $this->info("Iniciando backup para: {$system->name}");
            $this->backupSystem($system, $destinations);
        }

        $this->cleanupOldBackups();
        
        $this->info('Backup diário concludedo com sucesso.');
        return Command::SUCCESS;
    }

    private function backupSystem(System $system, $destinations): void
    {
        $timestamp = Date::now()->format('Y-m-d_His');
        $filename = "{$system->slug}_{$timestamp}.sql.gz";
        $tempPath = storage_path("backups/{$filename}");

        File::ensureDirectoryDirectory(storage_path('backups'));

        $result = $this->dumpDatabase($system, $tempPath);

        if ($result !== Command::SUCCESS) {
            $this->error("Falha ao fazer dump do banco {$system->name}");
            return;
        }

        $fileSize = File::size($tempPath);
        
        foreach ($destinations as $destination) {
            $this->uploadToDestination($system, $tempPath, $fileSize, $destination);
        }

        if (config('app.env') === 'production') {
            File::delete($tempPath);
        }
    }

    private function dumpDatabase(System $system, string $tempPath): int
    {
        $dbHost = $system->db_host ?? config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = $system->db_port ?? config('database.connections.mysql.port', 3306);
        $dbName = $system->db_name ?? $system->slug;
        $dbUser = $system->db_username ?? config('database.connections.mysql.username', 'root');
        $dbPass = $system->db_password ?? config('database.connections.mysql.password', '');
        $dbType = $system->db_type ?? 'mysql';

        if ($dbType === 'pgsql') {
            $command = "PGPASSWORD='{$dbPass}' pg_dump -h {$dbHost} -p {$dbPort} -U {$dbUser} -Fc {$dbName} | gzip > {$tempPath}";
        } else {
            $command = "mysqldump -h {$dbHost} -P {$dbPort} -u {$dbUser} -p'{$dbPass}' --single-transaction --quick --lock-tables=false {$dbName} | gzip > {$tempPath}";
        }

        $process = Process::run($command, function ($output) {
            $this->info($output);
        });

        return $process->failed() ? Command::FAILURE : Command::SUCCESS;
    }

    private function uploadToDestination(System $system, string $filePath, int $fileSize, BackupDestination $destination): void
    {
        if ($destination->type === 's3') {
            $this->uploadToS3($system, $filePath, $fileSize, $destination);
        } elseif ($destination->type === 'mac') {
            $this->uploadToMac($system, $filePath, $fileSize, $destination);
        }
    }

    private function uploadToS3(System $system, string $filePath, int $fileSize, BackupDestination $destination): void
    {
        $s3Key = "backups/{$system->slug}/" . basename($filePath);

        try {
            config(['filesystems.disks.s3.key' => $destination->access_key]);
            config(['filesystems.disks.s3.secret' => $destination->secret_key]);
            config(['filesystems.disks.s3.region' => $destination->region]);
            config(['filesystems.disks.s3.bucket' => $destination->bucket]);

            Storage::disk('s3')->put($s3Key, file_get_contents($filePath), [
                'StorageClass' => 'STANDARD_IA',
                'ServerSideEncryption' => 'AES256',
            ]);

            $this->info("Upload S3 sucesso: {$s3Key}");
        } catch (\Exception $e) {
            $this->error("Falha upload S3: {$e->getMessage()}");
        }
    }

    private function uploadToMac(System $system, string $filePath, int $fileSize, BackupDestination $destination): void
    {
        $remotePath = $destination->remote_path . '/' . $system->slug . '/' . basename($filePath);

        $command = "scp -P {$destination->port} {$filePath} {$destination->username}@{$destination->host}:{$remotePath}";

        if ($destination->private_key_path) {
            $command = "scp -i {$destination->private_key_path} -P {$destination->port} {$filePath} {$destination->username}@{$destination->host}:{$remotePath}";
        }

        $process = Process::run($command, function ($output) {
            $this->info($output);
        });

        if ($process->failed()) {
            $this->error('Falha ao fazer upload para Mac');
        } else {
            $this->info("Upload Mac sucesso: {$remotePath}");
        }
    }

    private function cleanupOldBackups(): void
    {
        $destinations = BackupDestination::where('active', true)->get();

        foreach ($destinations as $destination) {
            if ($destination->type === 's3') {
                $this->cleanupS3($destination);
            } elseif ($destination->type === 'mac') {
                $this->cleanupMac($destination);
            }
        }
    }

    private function cleanupS3(BackupDestination $destination): void
    {
        $cutoffDate = Date::now()->subDays($destination->retention_days);
        
        try {
            $files = Storage::disk('s3')->files("backups/");
            
            foreach ($files as $file) {
                $lastModified = Storage::disk('s3')->lastModified($file);
                
                if ($lastModified < $cutoffDate->timestamp) {
                    Storage::disk('s3')->delete($file);
                    $this->info("Arquivo expirado removido do S3: {$file}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Erro na limpeza do S3: {$e->getMessage()}");
        }
    }

    private function cleanupMac(BackupDestination $destination): void
    {
        $cutoffDate = Date::now()->subDays($destination->retention_days)->format('Y-m-d');
        $remotePath = $destination->remote_path;

        $command = "ssh -p {$destination->port} {$destination->username}@{$destination->host} 'find {$remotePath} -type f -mtime +{$destination->retention_days} -delete'";

        Process::run($command);
    }
}