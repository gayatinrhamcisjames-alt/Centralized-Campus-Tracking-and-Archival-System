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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('research_reference', 80)->nullable();
            $table->string('category', 60);
            $table->string('subject');
            $table->text('description');
            $table->string('priority', 20)->default('Normal');
            $table->string('status', 30)->default('Open');
            $table->string('attachment_path')->nullable();
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
        Schema::dropIfExists('support_tickets');
    }
};
