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
        Schema::create('weather_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weather_location_id')->constrained()->cascadeOnDelete();
            $table->decimal('temperature_celsius', 6, 2);
            $table->decimal('feels_like_celsius', 6, 2)->nullable();
            $table->unsignedTinyInteger('humidity_percent')->nullable();
            $table->decimal('pressure_hpa', 7, 2)->nullable();
            $table->decimal('wind_speed_kmh', 7, 2)->nullable();
            $table->unsignedSmallInteger('wind_direction_degrees')->nullable();
            $table->unsignedInteger('visibility_meters')->nullable();
            $table->decimal('uv_index', 5, 2)->nullable();
            $table->decimal('rainfall_mm', 7, 2)->nullable();
            $table->unsignedSmallInteger('weather_code');
            $table->string('condition');
            $table->string('weather_category', 20);
            $table->boolean('is_day')->default(true);
            $table->string('data_source', 50);
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->unique(['weather_location_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_records');
    }
};
