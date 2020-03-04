<?php
namespace LIQRGV\JurnalCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    const TABLE_NAME = 'issues';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    protected $fillable = [
        "site_id",
        "site_issue_id",
        "is_complete"
    ];

    protected $casts = [
        "is_complete" => "boolean",
    ];

    public function site() {
        $this->belongsTo(Site::class);
    }
}