<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherRecord extends Model
{
    protected $fillable = [
        'weather_location_id',
        'temperature_celsius',
        'feels_like_celsius',
        'humidity_percent',
        'pressure_hpa',
        'wind_speed_kmh',
        'wind_direction_degrees',
        'visibility_meters',
        'uv_index',
        'rainfall_mm',
        'weather_code',
        'condition',
        'weather_category',
        'is_day',
        'data_source',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature_celsius' => 'float',
            'feels_like_celsius' => 'float',
            'humidity_percent' => 'integer',
            'pressure_hpa' => 'float',
            'wind_speed_kmh' => 'float',
            'wind_direction_degrees' => 'integer',
            'visibility_meters' => 'integer',
            'uv_index' => 'float',
            'rainfall_mm' => 'float',
            'weather_code' => 'integer',
            'is_day' => 'boolean',
            'recorded_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WeatherLocation::class, 'weather_location_id');
    }
}
