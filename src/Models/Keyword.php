<?php
namespace LIQRGV\JurnalCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    const TABLE_NAME = 'keywords';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    protected $fillable = [
        "article_id",
        "keyword",
    ];


}