<?php

use App\Models\User;
use App\Models\WeatherLocation;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function openMeteoResponse(float $temperature = 29.4): array
{
    $localNow = now('Asia/Manila')->startOfHour();

    return [
        'current' => [
            'time' => $localNow->format('Y-m-d\TH:i'),
            'temperature_2m' => $temperature,
            'relative_humidity_2m' => 71,
            'apparent_temperature' => 31.2,
            'is_day' => 1,
            'precipitation' => 0,
            'weather_code' => 2,
            'pressure_msl' => 1011.4,
            'wind_speed_10m' => 12.5,
            'wind_direction_10m' => 135,
            'visibility' => 24000,
            'uv_index' => 6.1,
        ],
        'hourly' => [
            'time' => [$localNow->format('Y-m-d\TH:i')],
            'temperature_2m' => [30.1],
            'relative_humidity_2m' => [70],
            'precipitation' => [0.2],
            'precipitation_probability' => [20],
            'weather_code' => [2],
            'wind_speed_10m' => [14.0],
        ],
        'daily' => [
            'time' => [$localNow->toDateString()],
            'weather_code' => [2],
            'temperature_2m_min' => [25.0],
            'temperature_2m_max' => [32.0],
            'precipitation_sum' => [1.5],
            'precipitation_probability_max' => [30],
            'wind_speed_10m_max' => [18.0],
            'uv_index_max' => [8.0],
            'sunrise' => [$localNow->copy()->startOfDay()->addHours(6)->format('Y-m-d\TH:i')],
            'sunset' => [$localNow->copy()->startOfDay()->addHours(18)->format('Y-m-d\TH:i')],
        ],
    ];
}

test('an authenticated dashboard displays and stores current and forecast weather', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::response(openMeteoResponse()),
    ]);
    Cache::forget('weather:location:1:snapshot');
    Cache::forget('weather:location:1:failed-sync');
    $student = User::factory()->create(['role' => User::STUDENT]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertSee('Weather outlook')
        ->assertSee('Arakan')
        ->assertSee('North Cotabato, Philippines')
        ->assertSee('29')
        ->assertSee('°C')
        ->assertSee('Live');

    $this->assertDatabaseHas('weather_locations', [
        'slug' => 'arakan-north-cotabato',
        'name' => 'Arakan',
        'country' => 'North Cotabato, Philippines',
        'latitude' => 7.3521621,
        'longitude' => 125.1282103,
    ]);

    $this->assertDatabaseHas('weather_records', [
        'temperature_celsius' => 29.40,
        'condition' => 'Partly cloudy',
        'data_source' => 'open-meteo',
    ]);
    $this->assertDatabaseHas('weather_forecasts', [
        'period' => 'hourly',
        'temperature_celsius' => 30.10,
    ]);
    $this->assertDatabaseHas('weather_forecasts', [
        'period' => 'daily',
        'temperature_max_celsius' => 32.00,
    ]);

    Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), 'https://api.open-meteo.com/v1/forecast')
        && $request['latitude'] === 7.3521621
        && $request['longitude'] === 125.1282103);

    $cachedResponse = $this->actingAs($student)->get(route('student.dashboard'));

    $cachedResponse->assertSee('Cached');
    Http::assertSentCount(1);

    $adminResponse = $this->actingAs(User::factory()->create(['role' => User::ADMINISTRATOR]))
        ->get(route('admin.dashboard'));

    $adminResponse->assertSee('Open-Meteo Forecast API')
        ->assertSee('current.temperature_2m')
        ->assertSee('current.relative_humidity_2m')
        ->assertSee('current.wind_speed_10m')
        ->assertSee(config('services.open_meteo.forecast_url'))
        ->assertSee('Not required for this public forecast endpoint');
});

test('a dashboard shows the saved weather snapshot when the provider is unavailable', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::response([], 503),
    ]);
    $student = User::factory()->create(['role' => User::STUDENT]);
    $location = WeatherLocation::query()->create([
        'slug' => 'arakan-north-cotabato',
        'name' => 'Arakan',
        'country' => 'North Cotabato, Philippines',
        'timezone' => 'Asia/Manila',
        'latitude' => 7.3521621,
        'longitude' => 125.1282103,
        'last_synced_at' => now()->subHour(),
    ]);
    $location->weatherRecords()->create([
        'temperature_celsius' => 28.0,
        'feels_like_celsius' => 30.0,
        'humidity_percent' => 75,
        'pressure_hpa' => 1012,
        'wind_speed_kmh' => 10,
        'wind_direction_degrees' => 90,
        'visibility_meters' => 20000,
        'uv_index' => 5,
        'rainfall_mm' => 0,
        'weather_code' => 0,
        'condition' => 'Clear',
        'weather_category' => 'sunny',
        'is_day' => true,
        'data_source' => 'open-meteo',
        'recorded_at' => now()->subHour(),
    ]);
    Cache::forget('weather:location:'.$location->id.':snapshot');

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertSee('28')
        ->assertSee('°C')
        ->assertSee('Offline')
        ->assertSee('The live weather service is unavailable.');

    Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), 'https://api.open-meteo.com/v1/forecast'));
});

test('weather is only loaded on authenticated role dashboards', function () {
    Http::preventStrayRequests();

    $this->get(route('student.dashboard'))->assertRedirect(route('login'));
    $this->post(route('weather.refresh'))->assertRedirect(route('login'));
});

test('the scheduled weather sync command stores the latest provider data', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::response(openMeteoResponse()),
    ]);
    Cache::forget('weather:location:1:snapshot');

    $this->artisan('weather:sync')->assertSuccessful();

    $this->assertDatabaseHas('weather_records', [
        'temperature_celsius' => 29.40,
        'data_source' => 'open-meteo',
    ]);
});

test('the dashboard refresh requests fresh weather and updates the saved observation', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::sequence()
            ->push(openMeteoResponse())
            ->push(openMeteoResponse(30.4)),
    ]);
    Cache::forget('weather:location:1:snapshot');
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('Live');

    $this->from(route('admin.dashboard'))
        ->post(route('weather.refresh'))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseHas('weather_records', [
        'temperature_celsius' => 30.40,
        'data_source' => 'open-meteo',
    ]);
    Http::assertSentCount(2);
});

test('the dashboard stays usable when the provider returns an invalid response', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::response(['invalid' => 'weather payload']),
    ]);
    Cache::forget('weather:location:1:snapshot');
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertSee('Offline')
        ->assertSee('Weather data is not available yet')
        ->assertSee('The live weather service is unavailable');
    $this->assertDatabaseCount('weather_records', 0);
});

test('the dashboard stays usable when the weather provider connection fails', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::failedConnection(),
    ]);
    Cache::forget('weather:location:1:snapshot');
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertSee('Offline')
        ->assertSee('Weather data is not available yet')
        ->assertSee('The live weather service is unavailable');
});
