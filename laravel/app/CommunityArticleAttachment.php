<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityArticleAttachment extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'community_articles_attachments';

    protected $fillable = [
        'filename', 'location'
    ];

    public function article()
    {
        return $this->belongsTo('App\CommunityArticle');
    }

    public function getUrl()
    {
        return 'https://s3-us-west-2.amazonaws.com/data.twain.gosource.com.au/' . $this->location;
    }
}
