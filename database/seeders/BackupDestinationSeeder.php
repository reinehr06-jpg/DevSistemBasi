<?php

namespace Database\Seeders;

use App\Models\BackupDestination;
use Illuminate\Database\Seeder;

class BackupDestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Amazon S3 (Produção)',
                'type' => 's3',
                'bucket' => env('AWS_BUCKET', 'basileia-backups-prod'),
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'access_key' => env('AWS_ACCESS_KEY_ID'),
                'secret_key' => env('AWS_SECRET_ACCESS_KEY'),
                'retention_days' => 30,
                'active' => true,
            ],
            [
                'name' => 'Mac Local (Backup Local)',
                'type' => 'mac',
                'host' => env('BACKUP_MAC_HOST', '192.168.1.100'),
                'port' => (int) env('BACKUP_MAC_PORT', 22),
                'username' => env('BACKUP_MAC_USER', 'backup'),
                'private_key_path' => env('BACKUP_MAC_KEY', '~/.ssh/id_rsa'),
                'remote_path' => env('BACKUP_MAC_PATH', '/Volumes/Backup/basileia'),
                'retention_days' => 7,
                'active' => true,
            ],
        ];

        foreach ($destinations as $destination) {
            BackupDestination::updateOrCreate(
                ['name' => $destination['name']],
                $destination
            );
        }
    }
}