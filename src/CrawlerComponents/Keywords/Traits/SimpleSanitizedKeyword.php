<?php
namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\Traits;

use LIQRGV\JurnalCrawler\Helper\Helper;

trait SimpleSanitizedKeyword
{
    protected function sanitize(string $keyword): array
    {
        $keywordDelim = Helper::getDelimiter($keyword);
        return array_filter(
            str_replace('’',"'",
                preg_replace('/<a.*\/a>/', '',
                    explode($keywordDelim, $keyword)
                )
            )
        );
    }
}