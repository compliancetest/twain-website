<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
     use UuidTrait;

    public $table = 'products_features';

    public $incrementing = false;

    protected $fillable = ['test_suites_feature_id'];

    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id');
    }
}
