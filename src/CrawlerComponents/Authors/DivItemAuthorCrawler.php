<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Authors;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Author;

class DivItemAuthorCrawler extends BaseAuthorCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $authorCapture = Helper::getByRegexOnResponse($this->response, '/<span class="name">([\s\S]+?)<\/span>/');

        if (empty($authorCapture[1])) {
            return;
        }

        $authorsArray = $authorCapture[1];

        foreach ($authorsArray as $author) {
            Author::query()->insert([
                'article_id' => $this->articleId,
                'author_name' => trim($author),
            ]);
        }
    }
}