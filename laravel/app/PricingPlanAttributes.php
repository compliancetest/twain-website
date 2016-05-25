<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PricingPlanAttributes extends Model
{

    protected $table = 'wp_pricing_plans_attributes';

    public function plan()
    {
        return $this->belongsTo('\App\PricingPlan');
    }
}
