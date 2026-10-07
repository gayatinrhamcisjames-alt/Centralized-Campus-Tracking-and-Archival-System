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
        Schema::create('weather_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weather_location_id')->constrained()->cascadeOnDelete();
            $table->string('period', 10);
            $table->timestamp('forecast_at');
            $table->decimal('temperature_celsius', 6, 2)->nullable();
            $table->decimal('temperature_min_celsius', 6, 2)->nullable();
            $table->decimal('temperature_max_celsius', 6, 2)->nullable();
            $table->unsignedTinyInteger('humidity_percent')->nullable();
            $table->decimal('rainfall_mm', 7, 2)->nullable();
            $table->unsignedTinyInteger('precipitation_probability_percent')->nullable();
            $table->decimal('wind_speed_kmh', 7, 2)->nullable();
            $table->decimal('uv_index', 5, 2)->nullable();
            $table->unsignedSmallInteger('weather_code');
            $table->string('condition');
            $table->string('weather_category', 20);
            $table->timestamp('sunrise_at')->nullable();
            $table->timestamp('sunset_at')->nullable();
            $table->string('data_source', 50);
            $table->timestamps();
            $table->unique(['weather_location_id', 'period', 'forecast_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_forecasts');
    }
};
