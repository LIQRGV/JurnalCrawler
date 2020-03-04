<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Articles;


class BaseArticleCrawler
{
    protected $url;
    /**
     * @var int
     */
    protected $issueId;

    /**
     * BaseCrawler constructor.
     * @param string $url
     * @param int $issueId
     */
    public function __construct(string $url, int $issueId)
    {
        $this->url = $url;
        $this->issueId = $issueId;
    }
}