<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseSample extends Model
{
    use UuidTrait;

    public $table = 'test_cases_samples';

    public $incrementing = false;

    protected $fillable = ['test_case_id', 'image', 'description'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }
}
