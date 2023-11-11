<?php

declare(strict_types=1);

return [
    'definition' => base_path('integrations.yaml'),
    'token'      => [
        'github'  => env('GITHUB_TOKEN', ''), // you can replace this values based on your needs
        'example' => env('EXAMPLE_TOKEN', ''), // you can replace this values based on your needs
    ],
];
