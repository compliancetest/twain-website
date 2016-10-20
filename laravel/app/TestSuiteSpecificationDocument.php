<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteSpecificationDocument extends Model
{
     use UuidTrait;

    public $table = 'test_suites_specification_documents';

    public $incrementing = false;

    protected $fillable = ['name', 'description', 'link'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
