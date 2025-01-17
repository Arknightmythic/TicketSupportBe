<?php

namespace Database\Seeders;

use App\Models\Tickets;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Agent Support',
            'email' => 'support@tes.com',
            'role' => 'admin'
        ]);

        $users = User::factory(10)->create();
        Tickets::factory(10)
        ->state(
            new Sequence(
                fn(Sequence $sequence) =>[
                    'user_id' =>$users->random(),
                ]
            )
        )->create();
    }
}
