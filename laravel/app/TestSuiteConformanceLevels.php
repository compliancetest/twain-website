<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteConformanceLevels extends Model
{
     use UuidTrait;

    public $table = 'test_suites_conformance_levels';

    public $incrementing = false;

    protected $fillable = ['description', 'code'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
