<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LIQRGV\JurnalCrawler\Models\Article;
use LIQRGV\JurnalCrawler\Models\Author;
use LIQRGV\JurnalCrawler\Models\Keyword;
use LIQRGV\JurnalCrawler\Models\Site;

class AddIssueArticleRelation extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(Article::TABLE_NAME)) {
            Schema::table(Article::TABLE_NAME, function (Blueprint $table) {
                $table->addColumn('integer', 'issue_id', ['unsigned' => true]);
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
        if (Schema::hasTable(Article::TABLE_NAME)) {
            Schema::table(Article::TABLE_NAME, function (Blueprint $table) {
                $table->dropColumn('issue_id');
            });
        }
    }
}
