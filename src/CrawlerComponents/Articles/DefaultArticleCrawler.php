<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Articles;


use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Jobs\SaveArticleJob;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Site;

class DefaultArticleCrawler extends BaseArticleCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        /** @var Site $site */
        $site = Site::query()->where([
            'url' => $this->url,
        ])->firstOrFail();

        $targetUrl = preg_replace('/issue\/archive/', 'issue/view/' . $this->siteIssueId, $this->url);
        $archivePage = Helper::getPageFromUrl($targetUrl);
        $allArticles = Helper::getByRegexOnResponse($archivePage, '/http.+article\/view\/(\d+)/');
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

        foreach ($reversedArticleIds as $articleId) {
            $saveArticleJob = new SaveArticleJob($site, $this->issueId, $articleId);
            $dispatcher->dispatch($saveArticleJob);
        }

        Log::info("Done queueing crawler " . $this->url);
    }
}