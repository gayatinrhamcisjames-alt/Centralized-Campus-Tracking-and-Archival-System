<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'Admin')
            ->update(['role' => 'Administrator']);

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', [
                'Administrator',
                'Staff',
                'Thesis Coordinator',
                'Faculty Adviser',
                'Panel Member',
                'Student',
            ])
                ->default('Student')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('Student')->change();
        });

        DB::table('users')
            ->where('role', 'Administrator')
            ->update(['role' => 'Admin']);
    }
};
