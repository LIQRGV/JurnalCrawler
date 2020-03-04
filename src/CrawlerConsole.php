<?php

namespace LIQRGV\JurnalCrawler;

use Laravel\Lumen\Console\Kernel;
use LIQRGV\JurnalCrawler\Console\Crawler;

class CrawlerConsole extends Kernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Crawler::class,
    ];
}