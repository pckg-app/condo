<?php

return [
    'git' => [
        'bitbucket' => [
            'auth' => [
                'user' => dotenv('GIT_BB_USER'),
                'pass' => dotenv('GIT_BB_PASS'),
            ],
        ],
        'github' => [
            'auth' => [
                'user' => dotenv('GIT_GH_USER'),
                'pass' => dotenv('GIT_GH_PASS'),
                'token' => dotenv('GIT_GH_TOKEN'),
            ],
        ],
        'gitlab' => [
            'auth' => [
                'token' => dotenv('GIT_GL_TOKEN'),
            ]
        ],
    ],
    'trello' => [
        'key' => dotenv('TRELLO_KEY'),
        'token' => dotenv('TRELLO_TOKEN'),
        'oauth' => dotenv('TRELLO_OAUTH'),
    ],
];
