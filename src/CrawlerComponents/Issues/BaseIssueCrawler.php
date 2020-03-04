<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Issues;


class BaseIssueCrawler
{
    protected $url;

    /**
     * BaseCrawler constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }
}