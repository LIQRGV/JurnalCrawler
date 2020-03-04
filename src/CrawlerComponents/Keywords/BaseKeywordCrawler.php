<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use LIQRGV\JurnalCrawler\Models\Site;
use Psr\Http\Message\ResponseInterface;

class BaseKeywordCrawler
{
    /**
     * @var ResponseInterface
     */
    protected $respose;
    /**
     * @var Site
     */
    protected $site;
    /**
     * @var int
     */
    protected $articleId;

    /**
     * BaseCrawler constructor.
     * @param ResponseInterface $response
     * @param Site $site
     * @param int $articleId
     */
    public function __construct(ResponseInterface $response, Site $site, int $articleId)
    {
        $this->respose = $response;
        $this->site = $site;
        $this->articleId = $articleId;
    }
}