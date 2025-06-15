<?php

return [
    'enabled' => true,

    'report-only' => false,

    'report-uri' => null,

    'upgrade-insecure-requests' => false,

    'add-generated-nonce' => false,

    'directives' => [
        'default-src' => [
            'self',
        ],

        'script-src' => [
            'self',
            'unsafe-inline',
            'unsafe-eval',
            'https://cdn.jsdelivr.net',
            'https://nominatim.openstreetmap.org',
        ],

        'style-src' => [
            'self',
            'unsafe-inline',
            'https://fonts.googleapis.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
        ],

        'img-src' => [
            'self',
            'data:',
            'https://*.tile.openstreetmap.org',
            'https://cdn.jsdelivr.net',
        ],

        'font-src' => [
            'self',
            'https://fonts.gstatic.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
        ],

        'connect-src' => [
            'self',
            'https://nominatim.openstreetmap.org',
        ],
    ],
]; 