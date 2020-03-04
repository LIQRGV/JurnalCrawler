<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Keyword;

class DefaultKeywordCrawler extends BaseKeywordCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $keywordCapture = Helper::getByRegexOnResponse($this->respose, '/<div id="articleSubject">[\s\S]+?<div>([\s\S]+?)<\/div>/');

        if (empty($keywordCapture[1]) || empty($keywordCapture[1][0])) {
            return;
        }

        $keywordString = $keywordCapture[1][0];
        $keywordDelim = Helper::getDelimiter($keywordString);
        $keywordArray = explode($keywordDelim, $keywordString);

        foreach ($keywordArray as $keyword) {
            Keyword::query()->insert([
                'article_id' => $this->articleId,
                'keyword' => trim($keyword),
            ]);
        }
    }
}