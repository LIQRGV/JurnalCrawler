<?php

return [
    'providers' => [
        // for selecting queue
        // see Illuminate\Bus\Dispatcher#dispatch
        Illuminate\Bus\BusServiceProvider::class,

        Illuminate\Redis\RedisServiceProvider::class,
        Laravel\Horizon\HorizonServiceProvider::class,
    ],
];