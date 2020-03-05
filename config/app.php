<?php

return [
    'providers' => [
        // for selecting queue
        // see Illuminate\Bus\Dispatcher#dispatch
        Illuminate\Bus\BusServiceProvider::class,

        // for notification
        // see Illuminate\Contracts\Notifications\Dispatcher#send
        Illuminate\Notifications\NotificationServiceProvider::class,

        Illuminate\Redis\RedisServiceProvider::class,
        Laravel\Horizon\HorizonServiceProvider::class,
    ],
];