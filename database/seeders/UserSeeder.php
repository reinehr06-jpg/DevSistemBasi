<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@devsistem.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now()
        ]);
        
        echo "Usuário admin criado: admin@devsistem.com / admin123\n";
    }
}