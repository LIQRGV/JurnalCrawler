<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponent;


use Illuminate\Contracts\Bus\Dispatcher;

interface Crawlable
{
    function run(Dispatcher $dispatcher);
}