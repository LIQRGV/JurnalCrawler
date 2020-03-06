<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords;


use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\Traits\RegexSanitizedKeyword;
use LIQRGV\JurnalCrawler\Helper\Helper;
use Psr\Http\Message\ResponseInterface;

class SubjectBlockKataKunciDivKeywordCrawler extends RegexKeywordCrawler implements Crawlable
{
    use RegexSanitizedKeyword;

    function getKeywordCapture(ResponseInterface $response): array
    {
        return Helper::getByRegexOnResponse(
            $response,
            '/<div id="articleSubject" class="block">[\s\S]+?Kata [Kk]unci?[\s\S]+?<div>([\s\S]+?)<\/div>/'
        );
    }
}