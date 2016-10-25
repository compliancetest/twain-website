<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCapability extends Model
{
    use UuidTrait;

    public $table = 'products_capabilities';

    public $incrementing = false;

    protected $fillable = ['capability'];

    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id');
    }
}
