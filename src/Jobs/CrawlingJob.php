<?php

namespace LIQRGV\JurnalCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponents\CrawlerMethodFactory;

class CrawlingJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function handle(Dispatcher $dispatcher) {
        $crawlerMethodClass = CrawlerMethodFactory::getIssueCrawlerMethod($this->baseUrl);
        /** @var Crawlable $issueCrawlerMethod */
        $issueCrawlerMethod = new $crawlerMethodClass($this->baseUrl);
        $issueCrawlerMethod->run($dispatcher);
    }
}