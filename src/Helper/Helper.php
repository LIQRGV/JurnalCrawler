<?php

namespace LIQRGV\JurnalCrawler\Helper;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class Helper
{
    public static function getPageFromUrl(string $url) {
        Log::info("Crawling " . $url);
        $client = new Client();
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() != 200) {
            throw new \Exception("Error while crawling " . $url . ". Status: " . $response->getStatusCode());
        }

        return $response;
    }
    public static function getFirstRegexOnUrl(ResponseInterface $response, $regexToSearch, $tag = 'Entry')
    {
        $out = [];
        preg_match_all($regexToSearch, (string)$response->getBody(), $out);
        if ($out[0] && $out[0][0]) {
            // first match is the latest entry
            return $out[0][0];
        }

        throw new \Exception("No " . $tag . " found");
    }

    public static function getByRegexOnUrl(ResponseInterface $response, $regexToSearch)
    {
        $out = [];
        preg_match_all($regexToSearch, (string)$response->getBody(), $out);

        return $out;
    }
}