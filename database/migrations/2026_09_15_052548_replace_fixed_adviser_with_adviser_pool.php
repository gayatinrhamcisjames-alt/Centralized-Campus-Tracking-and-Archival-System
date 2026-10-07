<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $advisers = [
            ['name' => 'Maria Santos', 'email' => 'maria.santos@uis.test'],
            ['name' => 'Daniel Cruz', 'email' => 'daniel.cruz@uis.test'],
            ['name' => 'Angela Reyes', 'email' => 'angela.reyes@uis.test'],
            ['name' => 'Michael Garcia', 'email' => 'michael.garcia@uis.test'],
            ['name' => 'Sofia Mendoza', 'email' => 'sofia.mendoza@uis.test'],
        ];

        foreach ($advisers as $adviser) {
            DB::table('users')->updateOrInsert(
                ['email' => $adviser['email']],
                [
                    'name' => $adviser['name'],
                    'role' => 'Faculty Adviser',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        $replacementId = DB::table('users')
            ->where('email', $advisers[0]['email'])
            ->value('id');

        $rhamcisId = DB::table('users')->where('email', 'gayatin@gmail.com')->value('id');

        if ($rhamcisId !== null) {
            DB::table('theses')->where('adviser_id', $rhamcisId)->update(['adviser_id' => $replacementId]);
            DB::table('users')->where('id', $rhamcisId)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->whereIn('email', [
            'maria.santos@uis.test',
            'daniel.cruz@uis.test',
            'angela.reyes@uis.test',
            'michael.garcia@uis.test',
            'sofia.mendoza@uis.test',
        ])->delete();
    }
};
