<?php

namespace LIQRGV\JurnalCrawler\Helper;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class Helper
{
    public static function getPageFromUrl(string $url) {
        Log::info("Crawling " . $url);
        $client = new Client(
            ['headers' => ['User-Agent' => 'dummy']]
        );
        try {
            $response = $client->request('GET', $url);
        } catch (Exception $e) {
            $client = new Client([
                'verify' => false,
                'decode_content' => false,
                'headers' => ['User-Agent' => 'dummy']
            ]);
            $response = $client->request('GET', $url);
        }

        if ($response->getStatusCode() != 200) {
            throw new \Exception("Error while crawling " . $url . ". Status: " . $response->getStatusCode());
        }

        return $response;
    }
    public static function getFirstRegexOnResponse(ResponseInterface $response, $regexToSearch, $tag = 'Entry')
    {
        $out = [];
        preg_match_all($regexToSearch, (string)$response->getBody(), $out);
        if ($out[0] && $out[0][0]) {
            // first match is the latest entry
            return $out[0][0];
        }

        throw new \Exception("No " . $tag . " found");
    }

    public static function getByRegexOnResponse(ResponseInterface $response, $regexToSearch)
    {
        $out = [];
        preg_match_all($regexToSearch, (string)$response->getBody(), $out);

        return $out;
    }

    public static function getDelimiter(string $text) {
        if (strpos($text, ';') !== false) {
            return ';';
        }

        return ',';
    }
}