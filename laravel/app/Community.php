<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $table = 'communities';

    public $incrementing = false;

    protected $fillable = array('title', 'description', 'slug', 'creator_id', 'status');


    /**
     * @param Model $model
     * @param $value
     * @return string
     */
    public static function getUniqueSlug(\Illuminate\Database\Eloquent\Model $model, $value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count($model->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('App\CommunityMeta');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany('App\CommunityMembers');
    }

    /**
     * Get all approved community members
     * @return array|null
     */
    public function activeMembers()
    {
        return $this->members()->where('is_confirmed', true)->get();
    }

    /**
     * Get community member record
     * @return array|null
     */
    public function getMember($userId)
    {
        return $this->members()->where('user_id', $userId)->first();
    }

    /**
     * Get community admins
     * @return array|null
     */
    public function getAdmins()
    {
        return $this->members()->where('is_admin', true)->get();
    }

    /**
     * Get community admins
     * @return array|null
     */
    public function getModerators()
    {
        return $this->members()->where('is_mod', true)->get();
    }

    /**
     * Get usual community members
     * @return array|null
     */
    public function getMembers()
    {
        return $this->members()->where(['is_mod' => true, 'is_admin' => 'false', 'is_confirmed' => 'true'])->get();
    }

    /**
     * Check that given user is community admin
     * @return array|null
     */
    public function isAdmin($userId = false)
    {
        if (!$userId) {
            $userId = get_current_user_id();
        }
        return (boolean)$this->members()->where(['is_admin' => true, 'user_id' => $userId])->first();
    }

    /**
     * Get community URL
     * @return array|null
     */
    public function getUrl()
    {
        return home_url() . '/communities/' . $this->slug . '/';
    }


    /**
     * Get all community configs as assoc array
     * @return mixed
     */
    public function getAllMeta()
    {
        return $this->meta->keyBy('meta_key')->map(function ($item) {
            return $item['meta_value'];
        })->toArray();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public static function findBySlug($slug)
    {
        return Community::where(['slug' => $slug])->firstOrFail();
    }

    /**
     * get all not approved membership requests
     * @return mixed
     */
    public function getMembershipRequests()
    {
        return $this->members()->where(['is_confirmed' => 1])->get();
    }
}
