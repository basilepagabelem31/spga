<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the user can view the model.
    //  */
    // public function view(User $user, Product $product): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the user can create models.
    //  */
    // public function create(User $user): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the user can update the model.
    //  */
    // public function update(User $user, Product $product): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the user can delete the model.
    //  */
    // public function delete(User $user, Product $product): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the user can restore the model.
    //  */
    // public function restore(User $user, Product $product): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Product $product): bool
    // {
    //     return false;
    // }



    public function update(User $user, Product $product)
{
    // Un utilisateur peut mettre à jour un produit s'il est un partenaire
    // et que le produit est de type 'producteur_partenaire' et lui appartient
    return $user->partner && 
           $product->provenance_type === 'producteur_partenaire' && 
           $product->provenance_id === $user->partner->id;
}

public function delete(User $user, Product $product)
{
    // La même logique s'applique pour la suppression
    return $this->update($user, $product);
}
}
