<?php
namespace LIQRGV\JurnalCrawler\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use LIQRGV\JurnalCrawler\CrawlerComponent\Crawlable;
use LIQRGV\JurnalCrawler\CrawlerComponent\CrawlerMethodFactory;
use Symfony\Component\Console\Input\InputOption;

class Crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl';

    protected $description = 'crawl site with given url';

    public function __construct()
    {
        parent::__construct();
        $this->addOption("url", null, InputOption::VALUE_REQUIRED, "url to crawl");
    }

    public function handle(Dispatcher $dispatcher) {
        $url = $this->option('url');
        $crawlerMethodClass = CrawlerMethodFactory::getCrawlerMethod($url);
        /** @var Crawlable $crawlerMethod */
        $crawlerMethod = new $crawlerMethodClass($url);
        $crawlerMethod->run($dispatcher);
    }
}