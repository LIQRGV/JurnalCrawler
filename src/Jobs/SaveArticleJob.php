<?php

namespace LIQRGV\JurnalCrawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

    public function handle() {
        try {
            $this->crawlArticle($this->site, $this->articleId);
        } catch (\Exception $e) {
            Issue::query()->where([
                'site_id' => $this->site->id,
                'site_issue_id' => $this->issueId,
            ])->update([
                "is_complete" => false,
            ]);
        }
    }

    private function crawlArticle($site, int $articleId)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'article/view/' . $articleId, $site->url);

        $getDelimiter = function ($text) {
            if (strpos($text, ';') !== false) {
                return ';';
            }

            return ',';
        };

        $articlePage = Helper::getPageFromUrl($targetUrl);
        $authorCapture = Helper::getByRegexOnUrl($articlePage, '/<div id="authorString"><em>(.*)<\/em><\/div>/');

        if (empty($authorCapture[1]) || empty($authorCapture[1][0])) {
            return;
        }

        $keywordCapture = Helper::getByRegexOnUrl($articlePage, '/<div id="articleSubject">[\s\S]+?<div>([\s\S]+?)<\/div>/');

        if (empty($keywordCapture[1]) || empty($keywordCapture[1][0])) {
            return;
        }

        $authorsString = $authorCapture[1][0];
        $authorsDelim = $getDelimiter($authorsString);
        $authorsArray = explode($authorsDelim, $authorsString);

        $keywordString = $keywordCapture[1][0];
        $keywordDelim = $getDelimiter($keywordString);
        $keywordArray = explode($keywordDelim, $keywordString);

        $articleId = Article::query()->insertGetId([
            'site_id' => $site->id,
            'site_article_id' => $articleId,
            'url' => $targetUrl,
        ]);

        foreach ($authorsArray as $author) {
            Author::query()->insert([
                'article_id' => $articleId,
                'author_name' => trim($author),
            ]);
        }

        foreach ($keywordArray as $keyword) {
            Keyword::query()->insert([
                'article_id' => $articleId,
                'keyword' => trim($keyword),
            ]);
        }

        Log::info("Article " . $targetUrl . " saved");
    }
}