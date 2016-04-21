<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class Community extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'communities';

    protected $fillable = array('title', 'description', 'slug', 'creator_id', 'visibility_status', 'articles_status');


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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany('App\CommunityArticle');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profileTypes()
    {
        return $this->hasMany('App\ProfileType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany('App\Profile');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloads()
    {
        return $this->hasMany('App\CommunityDownloads');
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
     * Get CONFIRMED community member record
     * @return array|null
     */
    public function getActiveMember($userId)
    {
        return $this->members()->where(['user_id' => $userId, 'is_confirmed' => true])->first();
    }

    public function hasAccess($userId = false)
    {
        //non-logged in user cant view community content
        if (!Auth::check()) {
            return false;
        }
        if (!$userId) {
            $userId = Auth::user()->ID;
        }
        if ($this->getActiveMember($userId)) {
            return true;
        }
        return false;

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
        return $this->members()->where(['is_mod' => false, 'is_admin' => false, 'is_confirmed' => true])->get();
    }

    /**
     * Check that given user is community admin
     * @return array|null
     */
    public function isAdmin($userId = false)
    {
        if (!Auth::check()) {
            return false;
        }
        if (!$userId) {
            $userId = Auth::user()->ID;
        }
        return (boolean)$this->members()->where(['is_admin' => true, 'user_id' => $userId])->first();
    }

    /**
     * Get community URL
     * @return array|null
     */
    public function getUrl()
    {
        return getSiteUrl()  . '/communities/' . $this->slug . '/';
    }


    /**
     * Get one entry as string / all community configs as assoc array
     * @return mixed
     */
    public function getMeta($key = false)
    {
        if ($key) {
            return $this->meta->keyBy('meta_key')->map(function ($item) {
                return $item['meta_value'];
            })->get($key, null);
        }
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
        return $this->members()->where(['is_confirmed' => 0])->get();
    }

    public function getImageUrl()
    {
        if (!empty($this->image) && Storage::exists($this->image)) {
            return 'https://s3-us-west-2.amazonaws.com/data.twain.gosource.com.au/' . $this->image;
        }
        return getSiteUrl() . '/laravel/resources/assets/images/gravatar.jpg';
    }
}
