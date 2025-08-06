<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Category::query(); // Démarre une nouvelle requête Eloquent

        // Recherche par nom ou description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $categories = $query->paginate(10)->withQueryString();
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle catégorie.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Stocke une nouvelle catégorie dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie existante dans la base de données.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie de la base de données.
     */
    public function destroy(Category $category)
    {
        // Vérifier si des produits sont liés à cette catégorie avant de la supprimer
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                             ->with('error', 'Impossible de supprimer cette catégorie car des produits y sont liés.');
        }

        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie supprimée avec succès.');
    }
}
