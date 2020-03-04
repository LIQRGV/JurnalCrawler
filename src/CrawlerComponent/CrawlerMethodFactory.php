<?php

namespace LIQRGV\JurnalCrawler\CrawlerComponent;

use Exception;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\Helper\Helper;

class CrawlerMethodFactory
{
    public static function getCrawlerMethod(string $url)
    {
        Log::info("Get crawling method");
        if (self::isDefaultCrawler($url)) {
            Log::info("Using " . DefaultCrawler::class);
            return DefaultCrawler::class;
        }

        throw new Exception("No matching crawler");
    }

    private static function isDefaultCrawler(string $url)
    {
        $isArchive = self::isArchive($url);

        if (!$isArchive) {
            echo "URL is not base archive. Skip default crawler";
            return false;
        }

        try {
            $archivePage = Helper::getPageFromUrl($url);
            $latestIssue = Helper::getFirstRegexOnUrl($archivePage, '/http.+issue\/view\/\d+/', 'Issue');
            $issuePage = Helper::getPageFromUrl($latestIssue);
            Helper::getFirstRegexOnUrl($issuePage, '/http.+article\/view\/\d+/', 'Article');
        } catch (\Exception $e) {
            echo $e->getMessage() . ". Skip default crawler";
            return false;
        }

        return true;
    }

    private static function isArchive(string $url)
    {
        return ends_with($url, "/issue/archive");
    }
}