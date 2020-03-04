<?php

namespace LIQRGV\JurnalCrawler\CrawlerComponents;

use Exception;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponents\Articles\DefaultArticleCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Articles\TocArticleCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Authors\DefaultAuthorCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Issues\DefaultIssueCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\DefaultKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\NoKeywordCrawler;
use LIQRGV\JurnalCrawler\Helper\Helper;
use Psr\Http\Message\ResponseInterface;

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
        } else if (self::isTocArticleCrawler($url, $issueId)) {
            Log::info("Using " . TocArticleCrawler::class);
            return TocArticleCrawler::class;
        }

        throw new Exception("No matching article crawler");
    }

    public static function getAuthorCrawlerMethod(string $url, int $articleId)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'article/view/' . $articleId, $url);
        $articlePage = Helper::getPageFromUrl($targetUrl);

        Log::info("Get author crawler method");
        if (self::isDefaultAuthorCrawler($articlePage)) {
            Log::info("Using " . DefaultAuthorCrawler::class);
            return DefaultAuthorCrawler::class;
        }

        throw new Exception("No matching author crawler");
    }

    public static function getKeywordCrawlerMethod($url, int $articleId)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'article/view/' . $articleId, $url);
        $articlePage = Helper::getPageFromUrl($targetUrl);

        Log::info("Get author crawler method");
        if (self::isDefaultKeywordCrawler($articlePage)) {
            Log::info("Using " . DefaultKeywordCrawler::class);
            return DefaultKeywordCrawler::class;
        }

        Log::info("No keyword on " . $targetUrl . ". Using " . NoKeywordCrawler::class);
        return NoKeywordCrawler::class;
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
            echo $e->getMessage() . ". Skip default article crawler";
            return false;
        }

        return true;
    }

    private static function isTocArticleCrawler(string $url, int $issueId)
    {
        $targetUrl = preg_replace('/issue\/archive/', 'issue/view/' . $issueId . '\/showToc', $url);
        try {
            $issuePage = Helper::getPageFromUrl($targetUrl);
            Helper::getFirstRegexOnResponse($issuePage, '/http.+article\/view\/(\d+)/');
        } catch (\Exception $e) {
            echo $e->getMessage() . ". Skip toc article crawler";
            return false;
        }

        return true;
    }

    private static function isDefaultAuthorCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse($articlePage, '/<div id="authorString"><em>(.*)<\/em><\/div>/', 'Author');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip default author crawler";
            return false;
        }

        return true;
    }

    private static function isDefaultKeywordCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse($articlePage, '/<div id="articleSubject">[\s\S]+?<div>([\s\S]+?)<\/div>/', 'Keyword');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip default keyword crawler";
            return false;
        }

        return true;
    }

    private static function isArchive(string $url)
    {
        return ends_with($url, "/issue/archive");
    }
}