<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseRole extends Model
{
    use UuidTrait;

    public $table = 'test_cases_roles';

    public $incrementing = false;

    protected $fillable = ['test_suites_role_id'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }
}
