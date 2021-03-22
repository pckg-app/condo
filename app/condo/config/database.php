<?php

return [
    'default' => [
        'driver' => 'mysql',
        'host' => dotenv('DB_HOST', 'localhost'),
        'user' => dotenv('DB_USER', 'user'),
        'pass' => dotenv('DB_PASS', 'pass'),
        'db' => 'pckg_condo',
        'charset' => 'utf8',
    ],
];
