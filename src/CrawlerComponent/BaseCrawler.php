<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponent;


class BaseCrawler
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