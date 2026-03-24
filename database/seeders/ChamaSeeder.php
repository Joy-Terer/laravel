<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chama;

class ChamaSeeder extends Seeder
{
    public function run(): void
    {
        Chama::create([
            'name'                   => 'Wambua Chama',
            'description'            => 'A savings group for Wambua family and friends',
            'code'                   => 'WAMBUA01',
            'balance'                => 0.00,
            'contribution_amount'    => 2000.00,
            'contribution_frequency' => 'monthly',
        ]);
    }
}