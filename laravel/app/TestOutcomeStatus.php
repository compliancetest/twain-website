<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestOutcomeStatus extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = ['name', 'code'];

    public $timestamps = [];

    public static function getSuccessId()
    {
        return self::where(['code' => 'PASS'])->first()->id;
    }

    public static function getFailId()
    {
        return self::where(['code' => 'FAIL'])->first()->id;
    }

    public static function getInvalidZipId()
    {
        return self::where(['code' => 'INVALID_ZIP'])->first()->id;
    }
}
