<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $table = 'wp_usermeta';

    protected $primaryKey = 'umeta_id';

    public $timestamps = false;

    protected $fillable = [
        'meta_key', 'meta_value'
    ];

    public function meta()
    {
        return $this->belongsTo('\App\User', 'ID');
    }

}
