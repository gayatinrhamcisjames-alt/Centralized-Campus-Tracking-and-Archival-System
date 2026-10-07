<?php

namespace App\Services\Weather;

use App\Models\WeatherForecast;
use App\Models\WeatherLocation;
use App\Models\WeatherRecord;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;

class WeatherService
{
    public function __construct(private WeatherProvider $provider) {}

    /**
     * @return array{
     *     location: WeatherLocation,
     *     current: WeatherRecord|null,
     *     hourly: Collection<int, WeatherForecast>,
     *     daily: Collection<int, WeatherForecast>,
     *     mode: 'online'|'cached'|'offline',
     *     sync_error: string|null
     * }
     */
    public function dashboardData(): array
    {
        $location = $this->defaultLocation();

        $mode = 'offline';
        $syncError = null;
        $backoffKey = 'weather:location:'.$location->id.':failed-sync';
        $snapshotKey = 'weather:location:'.$location->id.':snapshot';

        if (Cache::has($backoffKey)) {
            $syncError = 'The live weather service is temporarily unavailable. Showing the latest saved observation, if available.';
        } else {
            try {
                $hasFreshSnapshot = Cache::has($snapshotKey);

                Cache::remember(
                    $snapshotKey,
                    now()->addMinutes((int) config('weather.dashboard_cache_minutes', 10)),
                    function () use ($location): bool {
                        $this->store($location, $this->provider->fetch($location));

                        return true;
                    },
                );

                Cache::forget($backoffKey);
                $location->refresh();
                $mode = $hasFreshSnapshot ? 'cached' : 'online';
            } catch (ConnectionException|RequestException|UnexpectedValueException $exception) {
                Cache::put($backoffKey, true, now()->addMinutes((int) config('weather.failure_backoff_minutes', 2)));
                Log::warning('Weather provider synchronization failed.', [
                    'location_id' => $location->id,
                    'exception' => $exception::class,
                ]);

                $syncError = 'The live weather service is unavailable. Showing the latest saved observation, if available.';
            }
        }

        $current = $location->weatherRecords()->latest('recorded_at')->first();
        $hourly = $location->forecasts()
            ->where('period', WeatherForecast::HOURLY)
            ->where('forecast_at', '>=', CarbonImmutable::now('UTC')->subHour())
            ->orderBy('forecast_at')
            ->limit(12)
            ->get();
        $daily = $location->forecasts()
            ->where('period', WeatherForecast::DAILY)
            ->where('forecast_at', '>=', CarbonImmutable::now($location->timezone)->startOfDay()->utc())
            ->orderBy('forecast_at')
            ->limit(7)
            ->get();

        return [
            'location' => $location,
            'current' => $current,
            'hourly' => $hourly,
            'daily' => $daily,
            'mode' => $mode,
            'sync_error' => $syncError,
        ];
    }

    /**
     * @return array{
     *     location: WeatherLocation,
     *     current: WeatherRecord|null,
     *     hourly: Collection<int, WeatherForecast>,
     *     daily: Collection<int, WeatherForecast>,
     *     mode: 'online'|'cached'|'offline',
     *     sync_error: string|null
     * }
     */
    public function refresh(): array
    {
        $location = $this->defaultLocation();

        Cache::forget('weather:location:'.$location->id.':snapshot');
        Cache::forget('weather:location:'.$location->id.':failed-sync');

        return $this->dashboardData();
    }

    /**
     * @param array{
     *     current: array<string, mixed>,
     *     hourly: list<array<string, mixed>>,
     *     daily: list<array<string, mixed>>
     * } $payload
     */
    private function store(WeatherLocation $location, array $payload): void
    {
        DB::transaction(function () use ($location, $payload): void {
            $current = $payload['current'];
            $recordedAt = $current['recorded_at'];
            unset($current['recorded_at']);

            $location->weatherRecords()->updateOrCreate(
                ['recorded_at' => $recordedAt],
                $current,
            );

            $forecasts = [];

            foreach ([
                WeatherForecast::HOURLY => $payload['hourly'],
                WeatherForecast::DAILY => $payload['daily'],
            ] as $period => $entries) {
                foreach ($entries as $entry) {
                    $forecasts[] = [
                        'weather_location_id' => $location->id,
                        'period' => $period,
                        'temperature_celsius' => null,
                        'temperature_min_celsius' => null,
                        'temperature_max_celsius' => null,
                        'humidity_percent' => null,
                        'rainfall_mm' => null,
                        'precipitation_probability_percent' => null,
                        'wind_speed_kmh' => null,
                        'uv_index' => null,
                        'sunrise_at' => null,
                        'sunset_at' => null,
                        ...$entry,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            WeatherForecast::query()->upsert(
                $forecasts,
                ['weather_location_id', 'period', 'forecast_at'],
                [
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
                    'updated_at',
                ],
            );

            $location->update(['last_synced_at' => now()]);
        });
    }

    private function defaultLocation(): WeatherLocation
    {
        return WeatherLocation::query()->firstOrCreate(
            ['slug' => config('weather.default_location.slug')],
            [
                'name' => config('weather.default_location.name'),
                'country' => config('weather.default_location.country'),
                'timezone' => config('weather.default_location.timezone'),
                'latitude' => config('weather.default_location.latitude'),
                'longitude' => config('weather.default_location.longitude'),
            ],
        );
    }
}
