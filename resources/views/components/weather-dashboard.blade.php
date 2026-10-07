@props(['weather', 'showIntegrationDetails' => false])

@php
    $current = $weather['current'];
    $timezone = $weather['location']->timezone;
    $weatherIcon = match ($current?->weather_category) {
        'sunny' => $current->is_day ? '☀️' : '🌙',
        'rain' => '🌧️',
        'storm' => '⛈️',
        'snow' => '❄️',
        'fog' => '🌫️',
        default => '☁️',
    };
    $weatherTheme = $current && ! $current->is_day && $current->weather_category === 'sunny'
        ? 'night'
        : ($current?->weather_category ?? 'unavailable');
@endphp

<section class="weather-dashboard-panel weather-tone-{{ $weatherTheme }}" aria-labelledby="weather-dashboard-title">
    <div class="weather-dashboard-heading">
        <div>
            <p class="weather-dashboard-eyebrow">{{ __('WeatherSphere · Local conditions') }}</p>
            <h3 id="weather-dashboard-title">{{ __('Weather outlook') }}</h3>
        </div>
        <div class="weather-dashboard-heading-actions">
            @php
                $modeLabel = match ($weather['mode']) {
                    'online' => __('Live'),
                    'cached' => __('Cached'),
                    default => __('Offline'),
                };
            @endphp
            <span class="weather-mode-badge is-{{ $weather['mode'] }}">
                <span aria-hidden="true"></span>{{ $modeLabel }}
            </span>
            <form method="POST" action="{{ route('weather.refresh') }}">
                @csrf
                <button class="weather-refresh-link" type="submit" aria-label="{{ __('Refresh weather from the live provider') }}">{{ __('Refresh') }} <span aria-hidden="true">↻</span></button>
            </form>
        </div>
    </div>

    @if (session('weather-sync-status'))
        <p class="weather-sync-notice" role="status">{{ __(session('weather-sync-status')) }}</p>
    @endif

    @if ($weather['sync_error'])
        <p class="weather-sync-notice" role="status">{{ __($weather['sync_error']) }}</p>
    @endif

    @if ($current)
        <div class="weather-dashboard-current">
            <div class="weather-current-copy">
                <p class="weather-location-label"><span aria-hidden="true">⌖</span>{{ $weather['location']->name }}, {{ $weather['location']->country }}</p>
                <p class="weather-condition">{{ $current->condition }}</p>
                <p class="weather-feels-like">{{ __('Feels like :temperature°C', ['temperature' => number_format($current->feels_like_celsius ?? $current->temperature_celsius, 0)]) }}</p>
                <p class="weather-updated-at">
                    {{ __('Updated :time', ['time' => $current->recorded_at->timezone($timezone)->diffForHumans()]) }}
                    <span aria-hidden="true">·</span>
                    {{ __('Synced :time', ['time' => $weather['location']->last_synced_at?->timezone($timezone)->diffForHumans() ?? __('unknown')]) }}
                </p>
            </div>
            <div class="weather-current-temperature">
                <span class="weather-current-icon" aria-hidden="true">{{ $weatherIcon }}</span>
                <strong>{{ number_format($current->temperature_celsius, 0) }}<sup>°C</sup></strong>
            </div>
        </div>

        <div class="weather-stat-grid">
            <article class="weather-stat-card"><span aria-hidden="true">💧</span><div><small>{{ __('Humidity') }}</small><strong>{{ $current->humidity_percent !== null ? $current->humidity_percent.'%' : '—' }}</strong></div></article>
            <article class="weather-stat-card"><span aria-hidden="true">💨</span><div><small>{{ __('Wind') }}</small><strong>{{ $current->wind_speed_kmh !== null ? number_format($current->wind_speed_kmh, 0).' km/h' : '—' }}</strong></div></article>
            <article class="weather-stat-card"><span aria-hidden="true">◉</span><div><small>{{ __('Pressure') }}</small><strong>{{ $current->pressure_hpa !== null ? number_format($current->pressure_hpa, 0).' hPa' : '—' }}</strong></div></article>
            <article class="weather-stat-card"><span aria-hidden="true">☀</span><div><small>{{ __('UV index') }}</small><strong>{{ $current->uv_index !== null ? number_format($current->uv_index, 1) : '—' }}</strong></div></article>
            <article class="weather-stat-card"><span aria-hidden="true">◌</span><div><small>{{ __('Visibility') }}</small><strong>{{ $current->visibility_meters !== null ? number_format($current->visibility_meters / 1000, 1).' km' : '—' }}</strong></div></article>
            <article class="weather-stat-card"><span aria-hidden="true">☂</span><div><small>{{ __('Rainfall') }}</small><strong>{{ $current->rainfall_mm !== null ? number_format($current->rainfall_mm, 1).' mm' : '—' }}</strong></div></article>
        </div>

        @if ($weather['hourly']->isNotEmpty())
            <div class="weather-forecast-section">
                <div class="weather-forecast-heading"><h4>{{ __('Next hours') }}</h4><span>{{ __('Local time') }}</span></div>
                <div class="weather-hourly-forecast">
                    @foreach ($weather['hourly'] as $forecast)
                        <div class="weather-hourly-item">
                            <span>{{ $forecast->forecast_at->timezone($timezone)->format('g A') }}</span>
                            <span class="weather-forecast-icon" aria-hidden="true">{{ $forecast->weather_category === 'sunny' ? '☀️' : ($forecast->weather_category === 'rain' ? '🌧️' : ($forecast->weather_category === 'storm' ? '⛈️' : ($forecast->weather_category === 'snow' ? '❄️' : '☁️'))) }}</span>
                            <strong>{{ $forecast->temperature_celsius !== null ? number_format($forecast->temperature_celsius, 0).'°' : '—' }}</strong>
                            <small>{{ $forecast->precipitation_probability_percent !== null ? $forecast->precipitation_probability_percent.'% rain' : ' ' }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($weather['daily']->isNotEmpty())
            <div class="weather-forecast-section weather-daily-section">
                <div class="weather-forecast-heading"><h4>{{ __('7-day forecast') }}</h4><span>{{ __('Daily outlook') }}</span></div>
                <div class="weather-daily-forecast">
                    @foreach ($weather['daily'] as $forecast)
                        <div class="weather-daily-item">
                            <span class="weather-daily-day">{{ $forecast->forecast_at->timezone($timezone)->isToday() ? __('Today') : $forecast->forecast_at->timezone($timezone)->format('D') }}</span>
                            <span class="weather-forecast-icon" aria-hidden="true">{{ $forecast->weather_category === 'sunny' ? '☀️' : ($forecast->weather_category === 'rain' ? '🌧️' : ($forecast->weather_category === 'storm' ? '⛈️' : ($forecast->weather_category === 'snow' ? '❄️' : '☁️'))) }}</span>
                            <span class="weather-daily-condition">{{ $forecast->condition }}</span>
                            <span class="weather-daily-rain">{{ $forecast->precipitation_probability_percent !== null ? $forecast->precipitation_probability_percent.'% rain' : '—' }}</span>
                            <strong>{{ $forecast->temperature_max_celsius !== null ? number_format($forecast->temperature_max_celsius, 0).'°' : '—' }}</strong>
                            <small>{{ $forecast->temperature_min_celsius !== null ? number_format($forecast->temperature_min_celsius, 0).'°' : '—' }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="weather-empty-state" role="status">
            <span aria-hidden="true">☁️</span>
            <div>
                <strong>{{ __('Weather data is not available yet.') }}</strong>
                <p>{{ __('The dashboard will show the latest saved conditions after the weather service synchronizes successfully.') }}</p>
            </div>
        </div>
    @endif

    @if ($showIntegrationDetails)
        <details class="weather-integration-details">
            <summary>{{ __('API integration details for demonstration') }}</summary>
            <div class="weather-integration-content">
                <p><strong>{{ __('Project title') }}:</strong> WeatherSphere weather dashboard</p>
                <p><strong>{{ __('Weather API / provider') }}:</strong> Open-Meteo Forecast API</p>
                <p><strong>{{ __('Endpoint') }}:</strong> <code>{{ config('services.open_meteo.forecast_url') }}</code></p>
                <p><strong>{{ __('API key') }}:</strong> {{ __('Not required for this public forecast endpoint.') }}</p>
                <p><strong>{{ __('Request and handling') }}:</strong> {{ __('Laravel HTTP Client sends a GET request with a 3-second connection timeout and an 8-second response timeout. HTTP failures, invalid JSON structures, and connection/timeouts are logged and fall back to saved weather data.') }}</p>
                <div class="weather-mapping-table-wrap">
                    <table class="weather-mapping-table">
                        <thead><tr><th>{{ __('API field / request parameter') }}</th><th>{{ __('Weather information displayed') }}</th></tr></thead>
                        <tbody>
                            <tr><td><code>latitude / longitude</code> {{ __('request parameters') }}</td><td>{{ __('Configured location: :location, :country', ['location' => $weather['location']->name, 'country' => $weather['location']->country]) }}</td></tr>
                            <tr><td><code>current.time</code></td><td>{{ __('Observation time and last update display') }}</td></tr>
                            <tr><td><code>current.temperature_2m</code></td><td>{{ __('Current temperature (°C)') }}</td></tr>
                            <tr><td><code>current.weather_code</code></td><td>{{ __('Weather condition and icon') }}</td></tr>
                            <tr><td><code>current.relative_humidity_2m</code></td><td>{{ __('Humidity (%)') }}</td></tr>
                            <tr><td><code>current.wind_speed_10m</code></td><td>{{ __('Wind speed (km/h)') }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="weather-provider-credit">{{ __('Weather data provided by Open-Meteo.') }}</p>
            </div>
        </details>
    @endif
</section>
