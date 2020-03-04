<?php

namespace LIQRGV\JurnalCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponent\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponent\CrawlerMethodFactory;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Issue;
use LIQRGV\JurnalCrawler\Models\Site;

class SaveIssueJob implements ShouldQueue
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

    public function __construct(Site $site, int $issueId)
    {
        $this->site = $site;
        $this->issueId = $issueId;
    }

    public function handle(Dispatcher $dispatcher) {
        $this->crawlIssue($this->site, $this->issueId, $dispatcher);
    }

    private function crawlIssue(Site $site, int $issueId, Dispatcher $dispatcher)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'issue/view/' . $issueId, $site->url);
        $issuePage = Helper::getPageFromUrl($targetUrl);
        $allArticles = Helper::getByRegexOnUrl($issuePage, '/http.+article\/view\/(\d+)/');
        if (empty($allArticles) || empty($allArticles[1])) {
            Log::info("No article found");
            return;
        }

        $articleIds = $allArticles[1];
        /** @var Collection $articlesOnDatabase */
        $articlesOnDatabase = Article::query()->where('site_id', $site->id)->pluck('site_article_id');

        $reversedArticleIds = array_filter(array_unique(array_reverse($articleIds)), function ($articleId) use ($articlesOnDatabase) {
            return !$articlesOnDatabase->contains($articleId);
        });

        Issue::query()->insert([
            'site_id' => $site->id,
            'site_issue_id' => $issueId,
            'is_complete' => true,
        ]);

        foreach ($reversedArticleIds as $articleId) {
            $saveArticleJob = new SaveArticleJob($site, $issueId, $articleId);
            $dispatcher->dispatch($saveArticleJob);
        }

        Log::info("Done queueing crawler " . $targetUrl);
    }
}