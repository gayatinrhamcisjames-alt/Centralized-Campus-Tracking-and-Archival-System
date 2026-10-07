<?php

return [
    'default_location' => [
        'slug' => env('WEATHER_DEFAULT_LOCATION_SLUG', 'antipas-north-cotabato'),
        'name' => env('WEATHER_DEFAULT_LOCATION', 'Arakan'),
        'country' => env('WEATHER_DEFAULT_COUNTRY', 'North Cotabato, Philippines'),
        'timezone' => env('WEATHER_DEFAULT_TIMEZONE', 'Asia/Manila'),
        'latitude' => (float) env('WEATHER_DEFAULT_LATITUDE', 7.3521621),
        'longitude' => (float) env('WEATHER_DEFAULT_LONGITUDE', 125.1282103),
    ],
    'dashboard_cache_minutes' => (int) env('WEATHER_DASHBOARD_CACHE_MINUTES', 10),
    'failure_backoff_minutes' => (int) env('WEATHER_FAILURE_BACKOFF_MINUTES', 2),
];
