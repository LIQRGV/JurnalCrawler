<?php

namespace LIQRGV\JurnalCrawler\CrawlerComponents;

use Exception;
use Illuminate\Support\Facades\Log;
use LIQRGV\JurnalCrawler\CrawlerComponents\Articles\DefaultArticleCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Articles\TocArticleCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Authors\DefaultAuthorCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Authors\DivItemAuthorCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Issues\DefaultIssueCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\AbstractKataKunciKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\AbstractKeywordKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\DefaultKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\DivItemKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\NoKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\SubjectBlockKataKunciDivKeywordCrawler;
use LIQRGV\JurnalCrawler\CrawlerComponents\Keywords\SubjectBlockKeywordDivKeywordCrawler;
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

        throw new Exception("No matching issue crawler for " . $url);
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
        } else if (self::isDivItemAuthorCrawler($articlePage)) {
            Log::info("Using " . DivItemAuthorCrawler::class);
            return DivItemAuthorCrawler::class;
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
        } else if (self::isDivItemKeywordCrawler($articlePage)) {
            Log::info("Using " . DivItemKeywordCrawler::class);
            return DivItemKeywordCrawler::class;
        } else if (self::isAbstractKeywordKeywordCrawler($articlePage)) {
            Log::info("Using " . AbstractKeywordKeywordCrawler::class);
            return AbstractKeywordKeywordCrawler::class;
        } else if (self::isSubjectBlockKeywordDivKeywordCrawler($articlePage)) {
            Log::info("Using " . SubjectBlockKeywordDivKeywordCrawler::class);
            return SubjectBlockKeywordDivKeywordCrawler::class;
        } else if (self::isAbstractKataKunciKeywordCrawler($articlePage)) {
            Log::info("Using " . AbstractKataKunciKeywordCrawler::class);
            return AbstractKataKunciKeywordCrawler::class;
        } else if (self::isSubjectBlockKataKunciDivKeywordCrawler($articlePage)) {
            Log::info("Using " . SubjectBlockKataKunciDivKeywordCrawler::class);
            return SubjectBlockKataKunciDivKeywordCrawler::class;
        }

        Log::info("No keyword on " . $targetUrl . ". Using " . NoKeywordCrawler::class);
        return NoKeywordCrawler::class;
    }

    private static function isDefaultIssueCrawler(string $url)
    {
        $isArchive = self::isArchive($url);

        if (!$isArchive) {
            echo "URL is not base archive. Skip default issue crawler";
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
        $targetUrl = preg_replace('/issue\/archive/', 'issue/view/' . $issueId . '/showToc', $url);
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
            Helper::getFirstRegexOnResponse($articlePage, '/<div id="authorString">[\s\S]+?<em>(.*)<\/em><\/div>/', 'Author');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip default author crawler";
            return false;
        }

        return true;
    }

    private static function isDivItemAuthorCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse($articlePage, '/<span class="name">([\s\S]+?)<\/span>/', 'Author');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip div item author crawler";
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

    private static function isDivItemKeywordCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse($articlePage, '/<div class="item keywords">[\s\S]+<span class="value">([\s\S]+?)<\/span>/', 'Keyword');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip div item keyword crawler";
            return false;
        }

        return true;
    }

    private static function isAbstractKeywordKeywordCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse($articlePage, '/<div id="articleAbstract">[\s\S]+[Kk]ey\s?[Ww]ords?([\s\S]+?)<\/p>/', 'Keyword');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip abstract keyword keyword crawler";
            return false;
        }

        return true;
    }

    private static function isSubjectBlockKeywordDivKeywordCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse(
                $articlePage,
                '/<div id="articleSubject" class="block">[\s\S]+?[Kk]ey\s?[Ww]ords?[\s\S]+?<div>([\s\S]+?)<\/div>/',
                'Keyword'
            );
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip subject block keyword div keyword crawler";
            return false;
        }

        return true;
    }

    private static function isAbstractKataKunciKeywordCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse($articlePage, '/<div id="articleAbstract">[\s\S]+Kata [Kk]unci?([\s\S]+?)<\/p>/', 'Keyword');
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip abstract kata kunci keyword crawler";
            return false;
        }

        return true;
    }

    private static function isSubjectBlockKataKunciDivKeywordCrawler(ResponseInterface $articlePage)
    {
        try {
            Helper::getFirstRegexOnResponse(
                $articlePage,
                '/<div id="articleSubject" class="block">[\s\S]+?Kata [Kk]unci?[\s\S]+?<div>([\s\S]+?)<\/div>/',
                'Keyword'
            );
        } catch (Exception $e) {
            echo $e->getMessage() . ". Skip subject block keyword div keyword crawler";
            return false;
        }

        return true;
    }

    private static function isArchive(string $url)
    {
        return ends_with($url, "/issue/archive");
    }
}