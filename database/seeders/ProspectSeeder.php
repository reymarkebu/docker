<?php

namespace Database\Seeders;
use App\Models\User;

use App\Models\Prospect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProspectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();

        Prospect::factory()->count(20)->create([
            'user_id' => fake()->randomElement($userIds),
        ]);
    }
}
