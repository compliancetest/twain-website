<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunitySurveyResult extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $table = 'community_surveys_results';

    protected $fillable = array('community_id', 'survey_id', 'author_id', 'link');

    public function community()
    {
        return $this->belongsTo('\App\Community');
    }

}
