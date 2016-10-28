<?php

namespace App\Policies;

use App\CommunityMembers;
use App\User;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Ensure that user can edit product
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function change(User $user, Product $product)
    {
        return $product->organisation_id == @$user->organisation[0]->id;
    }

    /**
     * Check that user can view product
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function view(User $user, Product $product)
    {
        if ($product->visibility == 'Public') {
            return true;
        }
        if ($product->visibility == 'Private') {
            return $product->organisation_id == @$user->organisation[0]->id;
        }
        if ($product->visibility == 'Community') {
            return $product->organisation_id == @$user->organisation[0]->id ||
                    CommunityMembers::where(['user_id' => $user->ID])->whereIn('community_id', CommunityMembers::where(['user_id' => $product->user_id])->pluck('community_id')->toArray())->exists();
        }
    }

    /**
     * Wordpress super admin can view / edit all products
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if (is_super_admin()) {
            return true;
        }
    }
}
