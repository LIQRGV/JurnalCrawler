<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Authors;


use LIQRGV\JurnalCrawler\Models\Site;
use Psr\Http\Message\ResponseInterface;

class BaseAuthorCrawler
{
    /**
     * @var ResponseInterface
     */
    protected $response;
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
        $this->response = $response;
        $this->site = $site;
        $this->articleId = $articleId;
    }
}