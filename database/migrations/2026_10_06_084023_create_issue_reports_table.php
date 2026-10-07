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
        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category', 60);
            $table->string('page_url')->nullable();
            $table->text('description');
            $table->text('reproduction_steps')->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('device', 120)->nullable();
            $table->string('severity', 20)->default('Minor');
            $table->string('screenshot_path')->nullable();
            $table->string('status', 30)->default('Open');
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reports');
    }
};
