<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaravelTestCase extends Model
{
    use UuidTrait;

    protected $table = 'test_cases';

    public $incrementing = false;

    protected $fillable = [
        'slug', 'name', 'version_major', 'version_minor', 'version_patch', 'description', 'full_name', 'tester_role',
        'harness_role', 'initiator', 'test_execution_profile_id', 'configuration_profile_id', 'outcome_type', 'is_optional', 'test_pattern',
        'wp_id', 'published_at', 'created_at', 'updated_at'
    ];

    public function conformanceLevels()
    {
        return $this->hasMany('\App\TestCaseConformanceLevel', 'test_case_id');
    }
}
