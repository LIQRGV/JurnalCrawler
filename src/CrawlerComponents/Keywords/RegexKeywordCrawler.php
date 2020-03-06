<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Keyword;
use Psr\Http\Message\ResponseInterface;

abstract class RegexKeywordCrawler extends BaseKeywordCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $keywordCapture = $this->getKeywordCapture($this->respose);

        if (empty($keywordCapture[1]) || empty($keywordCapture[1][0])) {
            return;
        }

        $sanitizedKeywordArray = $this->sanitize($keywordCapture[1][0]);

        foreach ($sanitizedKeywordArray as $keyword) {
            Keyword::query()->insert([
                'article_id' => $this->articleId,
                'keyword' => trim($keyword),
            ]);
        }
    }

    abstract function getKeywordCapture(ResponseInterface $response): array;
    abstract function sanitize(string $keywords): array;
}