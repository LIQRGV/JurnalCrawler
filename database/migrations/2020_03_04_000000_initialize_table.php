<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Author;
use LIQRGV\JurnalCrawler\Models\Keyword;
use LIQRGV\JurnalCrawler\Models\Site;

class InitializeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(Site::TABLE_NAME)) {
            Schema::create(Site::TABLE_NAME, function (Blueprint $table) {
                $table->increments('id');
                $table->string('url');
            });
        }

        if (!Schema::hasTable(Article::TABLE_NAME)) {
            Schema::create(Article::TABLE_NAME, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('site_id', false, true);
                $table->integer('site_article_id', false, true);
                $table->string('url');
                $table->string('abstract');
            });
        }

        if (!Schema::hasTable(Author::TABLE_NAME)) {
            Schema::create(Author::TABLE_NAME, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('article_id', false, true);
                $table->string('author_name');
            });
        }

        if (!Schema::hasTable(Keyword::TABLE_NAME)) {
            Schema::create(Keyword::TABLE_NAME, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('article_id', false, true);
                $table->string('keyword');
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
        Schema::dropIfExists(Site::TABLE_NAME);
        Schema::dropIfExists(Article::TABLE_NAME);
        Schema::dropIfExists(Author::TABLE_NAME);
        Schema::dropIfExists(Keyword::TABLE_NAME);
    }
}
