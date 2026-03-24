<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run():void
    {
        Superadmin::updateOrCreate([
            'email' => 'joytracycheptoo@gmail.com',
        ], [
            'name' => 'Mutai Joy',
            'password' => Hash::make('mutai#05'),
        ]);
    }
}