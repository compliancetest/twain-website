<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'slug', 'name', 'full_name', 'description', 'visibility', 'type', 'version',
        'manufacturer', 'protocol_version', 'model', 'access_url', 'organisation_id', 'user_id', 'wp_id',
        'created_at', 'updated_at'
    ];

    public function capabilities()
    {
        return $this->hasMany('App\ProductCapability');
    }

    public function features()
    {
        return $this->hasMany('App\ProductFeature');
    }
}
