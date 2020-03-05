<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Keyword;

class AbstractKeywordKeywordCrawler extends BaseKeywordCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $keywordCapture = Helper::getByRegexOnResponse($this->respose, '/<div id="articleAbstract">[\s\S]+[Kk]eywords:?([\s\S]+?)<\/p>/');

        if (empty($keywordCapture[1]) || empty($keywordCapture[1][0])) {
            return;
        }

        $keywordString = $keywordCapture[1][0];
        $keywordDelim = Helper::getDelimiter($keywordString);
        $keywordArray = explode($keywordDelim, $keywordString);
        $sanitizedKeywordArray = array_map(
            function($item) {
                $out = [];
                preg_match_all('/[a-zA-Z\-\s]+/', $item, $out);
                return $out[0][0];
            },
            str_replace(':', '',
                preg_replace('/<.*>/', '', $keywordArray))
        );

        foreach ($sanitizedKeywordArray as $keyword) {
            Keyword::query()->insert([
                'article_id' => $this->articleId,
                'keyword' => trim($keyword),
            ]);
        }
    }
}