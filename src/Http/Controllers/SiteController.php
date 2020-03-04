<?php
namespace LIQRGV\JurnalCrawler\Http\Controllers;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Request;
use Laravel\Lumen\Routing\Controller;
use LIQRGV\JurnalCrawler\Jobs\CrawlingJob;
use LIQRGV\JurnalCrawler\Models\Site;

class SiteController extends Controller
{
    public function index() {
        $sites = Site::all();

        return response([
            "data" => $sites->toArray(),
            "count" => $sites->count(),
        ]);
    }

    public function crawl(Dispatcher $dispatcher, Request $request, int $id) {
        $site = Site::query()->findOrFail($id);
        $crawlingJob = new CrawlingJob($site->url);
        $dispatcher->dispatch($crawlingJob);

        return response([], 201);
    }
}