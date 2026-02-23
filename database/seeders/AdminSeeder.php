<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin PPU AI',
            'email'    => 'rjjj1455@gmail.com',    // ganti email Anda
            'password' => Hash::make('password123'), // ganti password Anda
            'role'     => 'superadmin',
        ]);
    }
}