<?php
namespace LIQRGV\JurnalCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    const TABLE_NAME = 'authors';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    protected $fillable = [
        "article_id",
        "author_name",
    ];
}