<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseStep extends Model
{
    use UuidTrait;

    public $table = 'test_cases_test_steps';

    public $incrementing = false;

    protected $fillable = ['action', 'expected_result', 'step'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }
}
