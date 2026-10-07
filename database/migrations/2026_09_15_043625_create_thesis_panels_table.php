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
        Schema::create('thesis_panels', function (Blueprint $table) {
            $table->foreignId('thesis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('panel_member_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('Panel Member');
            $table->timestamps();

            $table->primary(['thesis_id', 'panel_member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_panels');
    }
};
