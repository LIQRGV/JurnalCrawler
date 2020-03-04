<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponent;


use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Jobs\SaveIssueJob;
use LIQRGV\JurnalCrawler\Models\Issue;
use LIQRGV\JurnalCrawler\Models\Site;

class DefaultCrawler extends BaseCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $site = Site::firstOrCreate([
            'url' => $this->url,
        ]);

        $archivePage = Helper::getPageFromUrl($this->url);
        $allIssues = Helper::getByRegexOnUrl($archivePage, '/http.+issue\/view\/(\d+)/');
        if (empty($allIssues) || empty($allIssues[1])) {
            Log::info("No issue found");
            return;
        }

        $issueIds = $allIssues[1];
        /** @var Collection $issuesOnDatabase */
        $issuesOnDatabase = Issue::query()->where([
            'site_id' => $site->id,
            'is_complete' => true,
        ])->pluck('site_issue_id');

        $reversedIssueIds = array_filter(array_unique(array_reverse($issueIds)), function ($issueId) use ($issuesOnDatabase) {
            return !$issuesOnDatabase->contains($issueId);
        });

        foreach ($reversedIssueIds as $issueId) {
            $saveIssueJob = new SaveIssueJob($site, $issueId);
            $dispatcher->dispatch($saveIssueJob);
        }

        Log::info("Done queueing crawler " . $this->url);
    }
}