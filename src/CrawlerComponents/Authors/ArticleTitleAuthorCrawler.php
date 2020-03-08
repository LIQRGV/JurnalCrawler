<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Authors;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Author;

class ArticleTitleAuthorCrawler extends BaseAuthorCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $authorCapture = Helper::getByRegexOnResponse(
            $this->response,
            '/<div id="articleTitle">[\s\S]*?<\/div>[\s\S]*?<br>([\s\S]+?)<br>/'
        );

        if (empty($authorCapture[1]) && empty($authorCapture[1][0])) {
            return;
        }

        $rawAuthorString = $authorCapture[1][0];
        $sanitizedAuthorString = trim(preg_replace('/<sup>.*?<\/sup>/', '', $rawAuthorString));
        $authorsArray = array_filter(explode(',', $sanitizedAuthorString));

        foreach ($authorsArray as $author) {
            Author::query()->insert([
                'article_id' => $this->articleId,
                'author_name' => trim($author),
            ]);
        }
    }
}