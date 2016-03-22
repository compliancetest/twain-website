<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [

    ];
}
