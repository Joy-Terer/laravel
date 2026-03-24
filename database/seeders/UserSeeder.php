<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chama;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $chama = Chama::first();

        // Admin (Chairperson)
        $admin = User::create([
            'chama_id'          => $chama->id,
            'name'              => 'Joy Tracy Mutai',
            'email'             => 'admin@smartchama.co.ke',
            'phone'             => '0712345678',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Link admin to chama
        $chama->update(['admin_id' => $admin->id]);

        // Treasurer
        User::create([
            'chama_id'          => $chama->id,
            'name'              => 'Peter Mwangi',
            'email'             => 'treasurer@smartchama.co.ke',
            'phone'             => '0723456789',
            'password'          => Hash::make('password'),
            'role'              => 'treasurer',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Active members
        $members = [
            ['name' => 'Ann Wangari',  'email' => 'ann@smartchama.co.ke',    'phone' => '0734567890'],
            ['name' => 'James Ouma',   'email' => 'james@smartchama.co.ke',  'phone' => '0745678901'],
            ['name' => 'Grace Njoki',  'email' => 'grace@smartchama.co.ke',  'phone' => '0756789012'],
            ['name' => 'Samuel Kamau', 'email' => 'samuel@smartchama.co.ke', 'phone' => '0767890123'],
            ['name' => 'Alice Akinyi', 'email' => 'alice@smartchama.co.ke',  'phone' => '0778901234'],
        ];

        foreach ($members as $member) {
            User::create([
                'chama_id'          => $chama->id,
                'name'              => $member['name'],
                'email'             => $member['email'],
                'phone'             => $member['phone'],
                'password'          => Hash::make('password'),
                'role'              => 'member',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Pending member — to test approval flow
        User::create([
            'chama_id' => $chama->id,
            'name'     => 'Diana Chebet',
            'email'    => 'diana@smartchama.co.ke',
            'phone'    => '0789012345',
            'password' => Hash::make('password'),
            'role'     => 'member',
            'status'   => 'pending',
        ]);
    }
}
