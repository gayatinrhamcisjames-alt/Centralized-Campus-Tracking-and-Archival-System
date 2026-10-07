<?php

namespace App\Services\Weather;

use App\Models\WeatherLocation;

interface WeatherProvider
{
    /**
     * @return array{
     *     current: array<string, mixed>,
     *     hourly: list<array<string, mixed>>,
     *     daily: list<array<string, mixed>>
     * }
     */
    public function fetch(WeatherLocation $location): array;
}
