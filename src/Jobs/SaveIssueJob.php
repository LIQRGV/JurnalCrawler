<?php

namespace LIQRGV\JurnalCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponents\CrawlerMethodFactory;
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
        $crawlerMethodClass = CrawlerMethodFactory::getArticleCrawlerMethod($this->site->url, $this->issueId);
        /** @var Crawlable $issueCrawlerMethod */
        $issueCrawlerMethod = new $crawlerMethodClass($this->site->url, $this->issueId);
        $issueCrawlerMethod->run($dispatcher);

        Issue::query()->updateOrInsert([
            'site_id' => $site->id,
            'site_issue_id' => $issueId,
        ], [
            'is_complete' => true,
        ]);
    }
}