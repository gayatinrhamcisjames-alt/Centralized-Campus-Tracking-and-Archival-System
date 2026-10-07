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
        DB::table('users')
            ->where('email', 'gayatin@gmail.com')
            ->update([
                'name' => 'Rhamcis James V Gayatin',
                'role' => 'Faculty Adviser',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('email', 'gayatin@gmail.com')
            ->update(['role' => 'Student']);
    }
};
