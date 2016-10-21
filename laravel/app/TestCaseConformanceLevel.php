<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseConformanceLevel extends Model
{
    use UuidTrait;

    public $table = 'test_cases_conformance_levels';

    public $incrementing = false;

    protected $fillable = ['conformance_level_id'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }
}
