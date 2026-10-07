<?php

namespace App\Services\Weather;

use App\Models\WeatherLocation;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

class OpenMeteoProvider implements WeatherProvider
{
    /**
     * @return array{
     *     current: array<string, mixed>,
     *     hourly: list<array<string, mixed>>,
     *     daily: list<array<string, mixed>>
     * }
     *
     * @throws ConnectionException
     */
    public function fetch(WeatherLocation $location): array
    {
        $response = Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(8)
            ->get(config('services.open_meteo.forecast_url'), [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'current' => implode(',', [
                    'temperature_2m',
                    'relative_humidity_2m',
                    'apparent_temperature',
                    'is_day',
                    'precipitation',
                    'weather_code',
                    'pressure_msl',
                    'wind_speed_10m',
                    'wind_direction_10m',
                    'visibility',
                    'uv_index',
                ]),
                'hourly' => implode(',', [
                    'temperature_2m',
                    'relative_humidity_2m',
                    'precipitation',
                    'precipitation_probability',
                    'weather_code',
                    'wind_speed_10m',
                ]),
                'daily' => implode(',', [
                    'weather_code',
                    'temperature_2m_min',
                    'temperature_2m_max',
                    'precipitation_sum',
                    'precipitation_probability_max',
                    'wind_speed_10m_max',
                    'uv_index_max',
                    'sunrise',
                    'sunset',
                ]),
                'timezone' => $location->timezone,
                'forecast_days' => 7,
                'forecast_hours' => 24,
                'temperature_unit' => 'celsius',
                'wind_speed_unit' => 'kmh',
                'precipitation_unit' => 'mm',
            ])
            ->throw();

        $payload = $response->json();

        if (! is_array($payload)
            || ! is_array($payload['current'] ?? null)
            || ! is_array($payload['hourly'] ?? null)
            || ! is_array($payload['daily'] ?? null)) {
            throw new UnexpectedValueException('The weather provider returned an invalid forecast response.');
        }

        return [
            'current' => $this->current($payload['current'], $location->timezone),
            'hourly' => $this->hourly($payload['hourly'], $location->timezone),
            'daily' => $this->daily($payload['daily'], $location->timezone),
        ];
    }

    /**
     * @param  array<string, mixed>  $current
     * @return array<string, mixed>
     */
    private function current(array $current, string $timezone): array
    {
        $weatherCode = (int) $this->requiredNumber($current, 'weather_code');

        return [
            'recorded_at' => $this->timestamp($this->requiredString($current, 'time'), $timezone),
            'temperature_celsius' => $this->requiredNumber($current, 'temperature_2m'),
            'feels_like_celsius' => $this->number($current, 'apparent_temperature'),
            'humidity_percent' => $this->number($current, 'relative_humidity_2m'),
            'pressure_hpa' => $this->number($current, 'pressure_msl'),
            'wind_speed_kmh' => $this->number($current, 'wind_speed_10m'),
            'wind_direction_degrees' => $this->number($current, 'wind_direction_10m'),
            'visibility_meters' => $this->number($current, 'visibility'),
            'uv_index' => $this->number($current, 'uv_index'),
            'rainfall_mm' => $this->number($current, 'precipitation'),
            'weather_code' => $weatherCode,
            'condition' => $this->condition($weatherCode),
            'weather_category' => $this->category($weatherCode),
            'is_day' => (bool) $this->requiredNumber($current, 'is_day'),
            'data_source' => 'open-meteo',
        ];
    }

    /**
     * @param  array<string, mixed>  $hourly
     * @return list<array<string, mixed>>
     */
    private function hourly(array $hourly, string $timezone): array
    {
        $times = $hourly['time'] ?? null;

        if (! is_array($times)) {
            throw new UnexpectedValueException('The weather provider returned no hourly forecast times.');
        }

        $forecasts = [];

        foreach (array_slice($times, 0, 24, true) as $index => $time) {
            if (! is_string($time)) {
                continue;
            }

            $weatherCode = (int) $this->requiredSeriesNumber($hourly, 'weather_code', $index);
            $forecasts[] = [
                'forecast_at' => $this->timestamp($time, $timezone),
                'temperature_celsius' => $this->seriesNumber($hourly, 'temperature_2m', $index),
                'humidity_percent' => $this->seriesNumber($hourly, 'relative_humidity_2m', $index),
                'rainfall_mm' => $this->seriesNumber($hourly, 'precipitation', $index),
                'precipitation_probability_percent' => $this->seriesNumber($hourly, 'precipitation_probability', $index),
                'wind_speed_kmh' => $this->seriesNumber($hourly, 'wind_speed_10m', $index),
                'weather_code' => $weatherCode,
                'condition' => $this->condition($weatherCode),
                'weather_category' => $this->category($weatherCode),
                'data_source' => 'open-meteo',
            ];
        }

        if ($forecasts === []) {
            throw new UnexpectedValueException('The weather provider returned no usable hourly forecasts.');
        }

        return $forecasts;
    }

    /**
     * @param  array<string, mixed>  $daily
     * @return list<array<string, mixed>>
     */
    private function daily(array $daily, string $timezone): array
    {
        $times = $daily['time'] ?? null;

        if (! is_array($times)) {
            throw new UnexpectedValueException('The weather provider returned no daily forecast times.');
        }

        $forecasts = [];

        foreach (array_slice($times, 0, 7, true) as $index => $time) {
            if (! is_string($time)) {
                continue;
            }

            $weatherCode = (int) $this->requiredSeriesNumber($daily, 'weather_code', $index);
            $forecasts[] = [
                'forecast_at' => $this->timestamp($time, $timezone),
                'temperature_min_celsius' => $this->seriesNumber($daily, 'temperature_2m_min', $index),
                'temperature_max_celsius' => $this->seriesNumber($daily, 'temperature_2m_max', $index),
                'rainfall_mm' => $this->seriesNumber($daily, 'precipitation_sum', $index),
                'precipitation_probability_percent' => $this->seriesNumber($daily, 'precipitation_probability_max', $index),
                'wind_speed_kmh' => $this->seriesNumber($daily, 'wind_speed_10m_max', $index),
                'uv_index' => $this->seriesNumber($daily, 'uv_index_max', $index),
                'weather_code' => $weatherCode,
                'condition' => $this->condition($weatherCode),
                'weather_category' => $this->category($weatherCode),
                'sunrise_at' => $this->seriesTimestamp($daily, 'sunrise', $index, $timezone),
                'sunset_at' => $this->seriesTimestamp($daily, 'sunset', $index, $timezone),
                'data_source' => 'open-meteo',
            ];
        }

        if ($forecasts === []) {
            throw new UnexpectedValueException('The weather provider returned no usable daily forecasts.');
        }

        return $forecasts;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredNumber(array $data, string $key): float
    {
        $value = $data[$key] ?? null;

        if (! is_numeric($value)) {
            throw new UnexpectedValueException("The weather provider omitted the {$key} value.");
        }

        return (float) $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function number(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new UnexpectedValueException("The weather provider omitted the {$key} value.");
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $series
     */
    private function seriesNumber(array $series, string $key, int $index): ?float
    {
        $values = $series[$key] ?? null;
        $value = is_array($values) ? ($values[$index] ?? null) : null;

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * @param  array<string, mixed>  $series
     */
    private function requiredSeriesNumber(array $series, string $key, int $index): float
    {
        $value = $this->seriesNumber($series, $key, $index);

        if ($value === null) {
            throw new UnexpectedValueException("The weather provider omitted the {$key} forecast value.");
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $series
     */
    private function seriesTimestamp(array $series, string $key, int $index, string $timezone): ?string
    {
        $values = $series[$key] ?? null;
        $value = is_array($values) ? ($values[$index] ?? null) : null;

        return is_string($value) ? $this->timestamp($value, $timezone) : null;
    }

    private function timestamp(string $timestamp, string $timezone): string
    {
        return CarbonImmutable::parse($timestamp, $timezone)->utc()->format('Y-m-d H:i:s');
    }

    private function condition(int $code): string
    {
        return match ($code) {
            0 => 'Clear',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45, 48 => 'Fog',
            51, 53, 55 => 'Drizzle',
            56, 57 => 'Freezing drizzle',
            61, 63, 65 => 'Rain',
            66, 67 => 'Freezing rain',
            71, 73, 75, 77 => 'Snow',
            80, 81, 82 => 'Rain showers',
            85, 86 => 'Snow showers',
            95, 96, 99 => 'Thunderstorm',
            default => 'Weather conditions unavailable',
        };
    }

    private function category(int $code): string
    {
        return match (true) {
            in_array($code, [95, 96, 99], true) => 'storm',
            in_array($code, [71, 73, 75, 77, 85, 86], true) => 'snow',
            in_array($code, [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82], true) => 'rain',
            in_array($code, [45, 48], true) => 'fog',
            $code === 0 => 'sunny',
            default => 'cloudy',
        };
    }
}
