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
            ['name' => 'Basileia Church', 'slug' => 'basileia-church', 'color' => '#8b5cf6', 'icon' => 'church'],
            ['name' => 'Basileia Finance', 'slug' => 'basileia-finance', 'color' => '#10b981', 'icon' => 'finance'],
            ['name' => 'Basile Ia', 'slug' => 'basile-ia', 'color' => '#f59e0b', 'icon' => 'ai'],
            ['name' => 'Basileia Vendor', 'slug' => 'basileia-vendor', 'color' => '#ef4444', 'icon' => 'vendor'],
            ['name' => 'Basileia Secure', 'slug' => 'basileia-secure', 'color' => '#3b82f6', 'icon' => 'secure'],
            ['name' => 'Basileia Proxy', 'slug' => 'basileia-proxy', 'color' => '#06b6d4', 'icon' => 'proxy'],
            ['name' => 'Basileia Desenvolvedor', 'slug' => 'basileia-desenvolvedor', 'color' => '#ec4899', 'icon' => 'developer'],
        ];

        foreach ($systems as $system) {
            System::create($system);
        }
    }
}