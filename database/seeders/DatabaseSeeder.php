<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'UIS Administrator', 'email' => 'admin@uis.test', 'role' => User::ADMINISTRATOR],
            ['name' => 'UIS Staff', 'email' => 'staff@uis.test', 'role' => User::STAFF],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@uis.test', 'role' => User::FACULTY_ADVISER],
            ['name' => 'Daniel Cruz', 'email' => 'daniel.cruz@uis.test', 'role' => User::FACULTY_ADVISER],
            ['name' => 'Angela Reyes', 'email' => 'angela.reyes@uis.test', 'role' => User::FACULTY_ADVISER],
            ['name' => 'Michael Garcia', 'email' => 'michael.garcia@uis.test', 'role' => User::FACULTY_ADVISER],
            ['name' => 'Sofia Mendoza', 'email' => 'sofia.mendoza@uis.test', 'role' => User::FACULTY_ADVISER],
            ['name' => 'UIS Student', 'email' => 'student@uis.test', 'role' => User::STUDENT],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'email_verified_at' => now(),
                ],
            );
        }

        $this->call(HelpCenterSeeder::class);
    }
}
