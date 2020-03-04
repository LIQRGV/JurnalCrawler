<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Keyword;

class NoKeywordCrawler extends BaseKeywordCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
    }
}