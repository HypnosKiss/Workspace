<?php
return [
    //默认认证token
    'default' => 'api',

    /**
     * 认证token类型列表
     */

    'tokens' => [
        'api' => [
            'header' => 'X-Access-Token',
            'cache' => 'access-token',
            'expired' => env('AUTH_EXPIRED_TIME', 120)   //分
        ],

        'admin' => [
            'header' => 'X-Access-Token',
            'cache' => 'admin-access-token',
            'expired' => env('AUTH_ADMIN_EXPIRED_TIME', 120)   //分
        ]
    ]
];
