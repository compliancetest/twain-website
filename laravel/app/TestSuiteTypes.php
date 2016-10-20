<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteTypes extends Model
{

    use UuidTrait;

    public $table = 'test_suites_types';

    public $incrementing = false;

    protected $fillable = ['type'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');

    }
}
