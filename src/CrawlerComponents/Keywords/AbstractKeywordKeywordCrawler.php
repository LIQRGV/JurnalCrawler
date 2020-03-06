<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\Traits\RegexSanitizedKeyword;
use LIQRGV\JurnalCrawler\Helper\Helper;
use Psr\Http\Message\ResponseInterface;

class AbstractKeywordKeywordCrawler extends RegexKeywordCrawler implements Crawlable
{
    use RegexSanitizedKeyword;

    function getKeywordCapture(ResponseInterface $response): array
    {
        return Helper::getByRegexOnResponse($response, '/<div id="articleAbstract">[\s\S]+[Kk]ey\s?[Ww]ords?([\s\S]+?)<\/p>/');;
    }
}