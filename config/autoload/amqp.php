<?php

return [
    'default' => [
        'host' => '8.129.176.243',
        'port' => 5672,
        'user' => 'zqp',
        'password' => 'Zqp113217',
        'vhost' => '/',
        'concurrent' => [
            'limit' => 1,
        ],
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
        ],
        'params' => [
            'insist' => false,
            'login_method' => 'AMQPLAIN',
            'login_response' => null,
            'locale' => 'en_US',
            'connection_timeout' => 3.0,
            'read_write_timeout' => 3.0,
            'context' => null,
            'keepalive' => false,
            'heartbeat' => 0,
            'close_on_destruct' => false,
        ],
    ]
];
