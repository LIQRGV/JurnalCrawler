<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Author;
use LIQRGV\JurnalCrawler\Models\Keyword;
use LIQRGV\JurnalCrawler\Models\Site;

class AddIndex extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(Site::TABLE_NAME)) {
            Schema::table(Site::TABLE_NAME, function (Blueprint $table) {
                $table->index(['url']);
            });
        }

        if (Schema::hasTable(Article::TABLE_NAME)) {
            Schema::table(Article::TABLE_NAME, function (Blueprint $table) {
                $table->index(['site_id', 'site_article_id']);
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
        if (Schema::hasTable(Site::TABLE_NAME)) {
            Schema::table(Site::TABLE_NAME, function (Blueprint $table) {
                $table->dropIndex(['url']);
            });
        }

        if (Schema::hasTable(Article::TABLE_NAME)) {
            Schema::table(Article::TABLE_NAME, function (Blueprint $table) {
                $table->dropIndex(['site_id', 'site_article_id']);
            });
        }
    }
}
