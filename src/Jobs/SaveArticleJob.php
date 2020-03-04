<?php

namespace LIQRGV\JurnalCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponents\CrawlerMethodFactory;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Author;
use LIQRGV\JurnalCrawler\Models\Issue;
use LIQRGV\JurnalCrawler\Models\Keyword;
use LIQRGV\JurnalCrawler\Models\Site;

class SaveArticleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Site
     */
    private $site;
    /**
     * @var int
     */
    private $issueId;
    /**
     * @var int
     */
    private $articleId;

    public function __construct(Site $site, int $issueId, int $articleId)
    {
        $this->site = $site;
        $this->issueId = $issueId;
        $this->articleId = $articleId;
    }

    public function handle(Dispatcher $dispatcher) {
        try {
            $this->crawlArticle($this->site, $this->articleId, $dispatcher);
        } catch (\Exception $e) {
            Issue::query()->where([
                'site_id' => $this->site->id,
                'site_issue_id' => $this->issueId,
            ])->update([
                "is_complete" => false,
            ]);
        }
    }

    private function crawlArticle($site, int $articleId, Dispatcher $dispatcher)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'article/view/' . $articleId, $site->url);
        $siteArticleId = $this->articleId;

        $articleId = Article::query()->insertGetId([
            'site_id' => $site->id,
            'site_article_id' => $articleId,
            'url' => $targetUrl,
            'issue_id' => $this->issueId,
        ]);

        $getDelimiter = function ($text) {
            if (strpos($text, ';') !== false) {
                return ';';
            }

            return ',';
        };

        $articlePage = Helper::getPageFromUrl($targetUrl);

        $crawlerAuthorMethodClass = CrawlerMethodFactory::getAuthorCrawlerMethod($this->site->url, $siteArticleId);
        /** @var Crawlable $issueCrawlerMethod */
        $authorCrawlerMethod = new $crawlerAuthorMethodClass($articlePage, $this->site, $articleId);
        $authorCrawlerMethod->run($dispatcher);

        $crawlerKeywordMethodClass = CrawlerMethodFactory::getKeywordCrawlerMethod($this->site->url, $siteArticleId);
        /** @var Crawlable $issueCrawlerMethod */
        $keywordCrawlerMethod = new $crawlerKeywordMethodClass($articlePage, $this->site, $articleId);
        $keywordCrawlerMethod->run($dispatcher);

        Log::info("Article " . $targetUrl . " queued");
    }
}