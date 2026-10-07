<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherForecast extends Model
{
    public const HOURLY = 'hourly';

    public const DAILY = 'daily';

    protected $fillable = [
        'weather_location_id',
        'period',
        'forecast_at',
        'temperature_celsius',
        'temperature_min_celsius',
        'temperature_max_celsius',
        'humidity_percent',
        'rainfall_mm',
        'precipitation_probability_percent',
        'wind_speed_kmh',
        'uv_index',
        'weather_code',
        'condition',
        'weather_category',
        'sunrise_at',
        'sunset_at',
        'data_source',
    ];

    protected function casts(): array
    {
        return [
            'forecast_at' => 'datetime',
            'temperature_celsius' => 'float',
            'temperature_min_celsius' => 'float',
            'temperature_max_celsius' => 'float',
            'humidity_percent' => 'integer',
            'rainfall_mm' => 'float',
            'precipitation_probability_percent' => 'integer',
            'wind_speed_kmh' => 'float',
            'uv_index' => 'float',
            'weather_code' => 'integer',
            'sunrise_at' => 'datetime',
            'sunset_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WeatherLocation::class, 'weather_location_id');
    }
}
