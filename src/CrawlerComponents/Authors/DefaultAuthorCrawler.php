<?php


namespace LIQRGV\JurnalCrawler\CrawlerComponents\Authors;


use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponents\Crawlable;
use LIQRGV\JurnalCrawler\Helper\Helper;
use LIQRGV\JurnalCrawler\Models\Author;

class DefaultAuthorCrawler extends BaseAuthorCrawler implements Crawlable
{
    function run(Dispatcher $dispatcher)
    {
        $authorCapture = Helper::getByRegexOnResponse($this->response, '/<div id="authorString">[\s\S]*?<em>(.*)<\/em><\/div>/');

        if (empty($authorCapture[1]) || empty($authorCapture[1][0])) {
            return;
        }

        $authorsString = $authorCapture[1][0];
        $authorsDelim = Helper::getDelimiter($authorsString);
        $authorsArray = array_filter(explode($authorsDelim, $authorsString));
        $sanitizedAuthorArray = str_replace('’', "'", $authorsArray);

        foreach ($sanitizedAuthorArray as $author) {
            $strippedDashAuthor = str_replace('-', '', $author);
            Author::query()->insert([
                'article_id' => $this->articleId,
                'author_name' => htmlspecialchars_decode(trim($strippedDashAuthor), ENT_QUOTES),
            ]);
        }
    }
}