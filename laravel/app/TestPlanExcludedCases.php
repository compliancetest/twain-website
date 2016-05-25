<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestPlanExcludedCases extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $table = 'test_plans_excluded_cases';

    protected $fillable = [
        'test_case_id', 'reason', 'excluded_by_user_id', 'date', 'test_plan_id'
    ];

    public function testPlan()
    {
        return $this->belongsTo('\App\Post', 'test_plan_id');
    }
}
