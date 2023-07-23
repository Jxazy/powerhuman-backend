<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\Team;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Responsibility;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Company::factory(10)->create();
        // Team::factory(30)->create();
        // Role::factory(20)->create();
        // Responsibility::factory(200)->create();
         Employee::factory(1000)->create();
    }
}
