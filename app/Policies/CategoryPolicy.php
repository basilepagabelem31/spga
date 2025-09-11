<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        // Uniquement si l'utilisateur est un partenaire et que la catégorie appartient à ce partenaire.
        return $user->partner && $user->partner->id === $category->provenance_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        // Uniquement si l'utilisateur est un partenaire et que la catégorie appartient à ce partenaire.
        return $user->partner && $user->partner->id === $category->provenance_id;
    }
}