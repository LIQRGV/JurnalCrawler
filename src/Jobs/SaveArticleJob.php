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
        $targetUrl = preg_replace('/issue\/archive/', 'article/view/' . $this->articleId, $this->site->url);
        $articleId = Article::query()->insertGetId([
            'site_id' => $this->site->id,
            'site_article_id' => $this->articleId,
            'url' => $targetUrl,
            'issue_id' => $this->issueId,
        ]);

        try {
            $this->crawlArticle($this->site, $this->articleId, $targetUrl, $articleId, $dispatcher);
        } catch (\Exception $e) {
            Log::error("Exception occurred on " . $targetUrl);
            Log::error($e->getTraceAsString());
            Issue::query()->where([
                'site_id' => $this->site->id,
                'site_issue_id' => $this->issueId,
            ])->update([
                "is_complete" => false,
            ]);

            Keyword::query()->where([
                'article_id' => $articleId,
            ])->delete();
            Author::query()->where([
                'article_id' => $articleId,
            ])->delete();
        }
    }

    private function crawlArticle($site, int $siteArticleId, string $targetUrl, $articleId, Dispatcher $dispatcher)
    {
        $articlePage = Helper::getPageFromUrl($targetUrl);

        $crawlerAuthorMethodClass = CrawlerMethodFactory::getAuthorCrawlerMethod($site->url, $siteArticleId);
        /** @var Crawlable $authorCrawlerMethod */
        $authorCrawlerMethod = new $crawlerAuthorMethodClass($articlePage, $site, $articleId);
        $authorCrawlerMethod->run($dispatcher);

        $crawlerKeywordMethodClass = CrawlerMethodFactory::getKeywordCrawlerMethod($site->url, $siteArticleId);
        /** @var Crawlable $keywordCrawlerMethod */
        $keywordCrawlerMethod = new $crawlerKeywordMethodClass($articlePage, $site, $articleId);
        $keywordCrawlerMethod->run($dispatcher);

        Log::info("Article " . $targetUrl . " queued");
    }
}