<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteProfileType extends Model
{
    use UuidTrait;

    public $table = 'test_suites_profile_types';

    public $incrementing = false;

    protected $fillable = ['profile_type_id'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
