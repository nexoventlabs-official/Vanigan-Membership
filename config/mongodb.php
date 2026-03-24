<?php

return [
    'url'      => env('MONGO_URL', 'mongodb://localhost:27017'),
    'database' => env('MONGO_DB_NAME', 'vanigan'),

    // Tracking MongoDB (incomplete registrations)
    'tracking_url'      => env('MONGO_TRACKING_URL', ''),
    'tracking_database' => env('MONGO_TRACKING_DB_NAME', 'vanigan_tracking'),
];
