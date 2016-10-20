<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteProtocolVersion extends Model
{
    use UuidTrait;

    public $table = 'test_suites_protocols_versions';

    public $incrementing = false;

    protected $fillable = ['version'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
