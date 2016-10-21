<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteRole extends Model
{
    use UuidTrait;

    public $table = 'test_suites_roles';

    public $incrementing = false;

    protected $fillable = ['name', 'description'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
