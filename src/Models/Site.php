<?php
namespace LIQRGV\JurnalCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    const TABLE_NAME = 'sites';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    protected $fillable = [
        "url",
    ];

    public function articles() {
        $this->hasMany(Article::class);
    }
}