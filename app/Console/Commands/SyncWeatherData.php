<?php

namespace App\Console\Commands;

use App\Services\Weather\WeatherService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('weather:sync')]
#[Description('Synchronize and store current weather and forecasts')]
class SyncWeatherData extends Command
{
    public function handle(WeatherService $weatherService): int
    {
        $weather = $weatherService->refresh();

        if ($weather['mode'] === 'offline') {
            $this->error($weather['sync_error'] ?? 'Weather synchronization failed.');

            return self::FAILURE;
        }

        $this->info('Weather synchronized for '.$weather['location']->name.'.');

        return self::SUCCESS;
    }
}
