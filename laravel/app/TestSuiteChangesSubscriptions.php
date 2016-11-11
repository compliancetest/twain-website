<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteChangesSubscriptions extends Model
{
    use UuidTrait;

    public $table = 'test_suites_changes_subscriptions';

    public $incrementing = false;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo('\App\User', 'user_id');
    }
}
