<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Author;
use LIQRGV\JurnalCrawler\Models\Keyword;
use LIQRGV\JurnalCrawler\Models\Site;

class AddFailedJobsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $failedJobTable = Config::get('queue.failed.table');
        if (!Schema::hasTable($failedJobTable)) {
            Schema::create($failedJobTable, function (Blueprint $table) {
                $table->increments('id');
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $failedJobTable = Config::get('queue.failed.table');
        Schema::dropIfExists($failedJobTable);
    }
}
