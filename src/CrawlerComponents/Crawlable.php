<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents;


use Illuminate\Contracts\Bus\Dispatcher;

interface Crawlable
{
    function run(Dispatcher $dispatcher);
}