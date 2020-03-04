<?php
namespace LIQRGV\JurnalCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    const TABLE_NAME = 'articles';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    protected $fillable = [
        "site_id",
        "site_article_id",
        "url",
        "abstract",
    ];

    public function authors() {
        return $this->hasMany(Author::class);
    }

    public function keywords() {
        return $this->hasMany(Keyword::class);
    }

    public function issue() {
        return $this->hasOne(Issue::class);
    }
}