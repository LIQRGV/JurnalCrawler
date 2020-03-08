<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\Traits\SimpleSanitizedKeyword;
use LIQRGV\JurnalCrawler\Helper\Helper;
use Psr\Http\Message\ResponseInterface;

class DivItemAbstractKeywordCrawler extends RegexKeywordCrawler implements Crawlable
{
    use SimpleSanitizedKeyword;

    function getKeywordCapture(ResponseInterface $response): array
    {
        return Helper::getByRegexOnResponse($response, '/<div class="item abstract">[\s\S]+<p>[\s\S]+Kata [Kk]unci: ([\s\S]+?)<\/p>/');
    }
}