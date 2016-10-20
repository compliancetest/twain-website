<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteRelatedSuite extends Model
{
    use UuidTrait;

    public $table = 'test_suites_related_suites';

    public $incrementing = false;

    protected $fillable = ['related_test_suite_id'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
