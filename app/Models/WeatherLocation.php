<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeatherLocation extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'country',
        'timezone',
        'latitude',
        'longitude',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'last_synced_at' => 'datetime',
        ];
    }

    public function weatherRecords(): HasMany
    {
        return $this->hasMany(WeatherRecord::class);
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(WeatherForecast::class);
    }
}
