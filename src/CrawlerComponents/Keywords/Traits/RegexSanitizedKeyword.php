<?php
namespace LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\Traits;

use LIQRGV\JurnalCrawler\Helper\Helper;

trait RegexSanitizedKeyword
{
    protected function sanitize(string $keyword): array
    {
        $keywordDelim = Helper::getDelimiter($keyword);
        $keywordArray = explode($keywordDelim, $keyword);
        return array_map(
            function($item) {
                $out = [];
                preg_match_all('/[a-zA-Z\-\s]+/', $item, $out);
                return $out[0][0];
            },
            array_filter(
                str_replace('’',"'",
                    str_replace(':', '',
                        preg_replace('/<.*?>/', '',
                            preg_replace('/<a.*\/a>/', '', $keywordArray)))
                )
            )
        );
    }
}