<?php

namespace App\Policies;

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
        return $product->organisation_id == @$user->organisation[0]->id || is_super_admin();
    }
}
