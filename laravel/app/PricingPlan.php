<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{

    protected $table = 'wp_pricing_plans';

    public function attributes()
    {
        return $this->hasMany('\App\PricingPlanAttributes');
    }
}
