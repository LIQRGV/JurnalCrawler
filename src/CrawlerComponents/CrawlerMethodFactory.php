<?php

namespace LIQRGV\JurnalCrawler\CrawlerComponents;

use Exception;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponents\Articles\DefaultArticleCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Issues\DefaultIssueCrawler;
use LIQRGV\JurnalCrawler\Helper\Helper;

class CrawlerMethodFactory
{
    public static function getIssueCrawlerMethod(string $url)
    {
        Log::info("Get issue crawler method");
        if (self::isDefaultIssueCrawler($url)) {
            Log::info("Using " . DefaultIssueCrawler::class);
            return DefaultIssueCrawler::class;
        }

        throw new Exception("No matching issue crawler");
    }

    public static function getArticleCrawlerMethod(string $url, int $issueId)
    {
        Log::info("Get article crawler method");
        if (self::isDefaultArticleCrawler($url, $issueId)) {
            Log::info("Using " . DefaultArticleCrawler::class);
            return DefaultArticleCrawler::class;
        }

        throw new Exception("No matching article crawler");
    }

    private static function isDefaultIssueCrawler(string $url)
    {
        $isArchive = self::isArchive($url);

        if (!$isArchive) {
            echo "URL is not base archive. Skip default crawler";
            return false;
        }

        try {
            $archivePage = Helper::getPageFromUrl($url);
            Helper::getFirstRegexOnResponse($archivePage, '/http.+issue\/view\/\d+/', 'Issue');
        } catch (\Exception $e) {
            echo $e->getMessage() . ". Skip default issue crawler";
            return false;
        }

        return true;
    }

    private static function isDefaultArticleCrawler(string $url, int $issueId)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'issue/view/' . $issueId, $url);
        try {
            $issuePage = Helper::getPageFromUrl($targetUrl);
            Helper::getFirstRegexOnResponse($issuePage, '/http.+article\/view\/(\d+)/');
        } catch (\Exception $e) {
            echo $e->getMessage() . ". Skip default issue crawler";
            return false;
        }

        return true;
    }

    private static function isArchive(string $url)
    {
        return ends_with($url, "/issue/archive");
    }
}