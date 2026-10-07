<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->string('department')->nullable()->after('program');
            $table->string('school_year', 20)->nullable()->after('academic_year');
            $table->date('defense_date')->nullable()->after('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn(['department', 'school_year', 'defense_date']);
        });
    }
};
