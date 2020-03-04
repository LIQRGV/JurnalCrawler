<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Author;
use LIQRGV\JurnalCrawler\Models\Issue;
use LIQRGV\JurnalCrawler\Models\Keyword;
use LIQRGV\JurnalCrawler\Models\Site;

class AddIssuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(Issue::TABLE_NAME)) {
            Schema::create(Issue::TABLE_NAME, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('site_id', false, true);
                $table->integer('site_issue_id');
                $table->boolean('is_complete');

                $table->index(['site_id', 'site_issue_id']);
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
        Schema::dropIfExists(Issue::TABLE_NAME);
    }
}
