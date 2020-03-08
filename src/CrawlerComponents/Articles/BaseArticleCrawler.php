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
     * @var int
     */
    protected $siteIssueId;

    /**
     * BaseCrawler constructor.
     * @param string $url
     * @param int $issueId
     * @param int $siteIssueId
     */
    public function __construct(string $url, int $issueId, int $siteIssueId)
    {
        $this->url = $url;
        $this->issueId = $issueId;
        $this->siteIssueId = $siteIssueId;
    }
}