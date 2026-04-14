<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\System;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            [
                'name' => 'Basileia Church',
                'slug' => 'basileia-church',
                'color' => '#8b5cf6',
                'icon' => 'church',
                'active' => true,
                'db_type' => 'mysql',
                'db_host' => env('BASILEIA_CHURCH_DB_HOST', '127.0.0.1'),
                'db_port' => env('BASILEIA_CHURCH_DB_PORT', 3306),
                'db_name' => env('BASILEIA_CHURCH_DB_NAME', 'basileia_church'),
                'db_username' => env('BASILEIA_CHURCH_DB_USER', 'root'),
                'db_password' => env('BASILEIA_CHURCH_DB_PASS', ''),
            ],
            [
                'name' => 'Basileia Finance',
                'slug' => 'basileia-finance',
                'color' => '#10b981',
                'icon' => 'finance',
                'active' => true,
                'db_type' => 'mysql',
                'db_host' => env('BASILEIA_FINANCE_DB_HOST', '127.0.0.1'),
                'db_port' => env('BASILEIA_FINANCE_DB_PORT', 3306),
                'db_name' => env('BASILEIA_FINANCE_DB_NAME', 'basileia_finance'),
                'db_username' => env('BASILEIA_FINANCE_DB_USER', 'root'),
                'db_password' => env('BASILEIA_FINANCE_DB_PASS', ''),
            ],
            [
                'name' => 'Basileia IA',
                'slug' => 'basileia-ia',
                'color' => '#f59e0b',
                'icon' => 'ai',
                'active' => true,
                'db_type' => 'mysql',
                'db_host' => env('BASILEIA_IA_DB_HOST', '127.0.0.1'),
                'db_port' => env('BASILEIA_IA_DB_PORT', 3306),
                'db_name' => env('BASILEIA_IA_DB_NAME', 'basileia_ia'),
                'db_username' => env('BASILEIA_IA_DB_USER', 'root'),
                'db_password' => env('BASILEIA_IA_DB_PASS', ''),
            ],
            [
                'name' => 'Basileia Vendor',
                'slug' => 'basileia-vendor',
                'color' => '#ef4444',
                'icon' => 'vendor',
                'active' => true,
                'db_type' => 'mysql',
                'db_host' => env('BASILEIA_VENDOR_DB_HOST', '127.0.0.1'),
                'db_port' => env('BASILEIA_VENDOR_DB_PORT', 3306),
                'db_name' => env('BASILEIA_VENDOR_DB_NAME', 'basileia_vendor'),
                'db_username' => env('BASILEIA_VENDOR_DB_USER', 'root'),
                'db_password' => env('BASILEIA_VENDOR_DB_PASS', ''),
            ],
            [
                'name' => 'Basileia Secure',
                'slug' => 'basileia-secure',
                'color' => '#3b82f6',
                'icon' => 'secure',
                'active' => true,
                'db_type' => 'mysql',
                'db_host' => env('BASILEIA_SECURE_DB_HOST', '127.0.0.1'),
                'db_port' => env('BASILEIA_SECURE_DB_PORT', 3306),
                'db_name' => env('BASILEIA_SECURE_DB_NAME', 'basileia_secure'),
                'db_username' => env('BASILEIA_SECURE_DB_USER', 'root'),
                'db_password' => env('BASILEIA_SECURE_DB_PASS', ''),
            ],
            [
                'name' => 'Basileia Proxy',
                'slug' => 'basileia-proxy',
                'color' => '#06b6d4',
                'icon' => 'proxy',
                'active' => true,
                'db_type' => 'pgsql',
                'db_host' => env('BASILEIA_PROXY_DB_HOST', '127.0.0.1'),
                'db_port' => env('BASILEIA_PROXY_DB_PORT', 5432),
                'db_name' => env('BASILEIA_PROXY_DB_NAME', 'basileia_proxy'),
                'db_username' => env('BASILEIA_PROXY_DB_USER', 'postgres'),
                'db_password' => env('BASILEIA_PROXY_DB_PASS', ''),
            ],
            [
                'name' => 'Basileia Dev',
                'slug' => 'basileia-dev',
                'color' => '#ec4899',
                'icon' => 'developer',
                'active' => true,
                'db_type' => 'sqlite',
                'db_name' => database_path('database.sqlite'),
            ],
        ];

        foreach ($systems as $system) {
            System::updateOrCreate(
                ['slug' => $system['slug']],
                $system
            );
        }
    }
}