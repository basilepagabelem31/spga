<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Seules les catégories générales (provenance_id est null)
        $query = Category::whereNull('provenance_id');

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

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,provenance_id,NULL', // Validation unique pour les catégories générales
            'description' => 'nullable|string',
        ]);

        // Assurez-vous que provenance_id est null pour les catégories d'admin
        $validatedData['provenance_id'] = null;
        
        Category::create($validatedData);

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category)
    {
        // On s'assure qu'un admin ne peut éditer que les catégories générales
        if ($category->provenance_id !== null) {
            abort(403);
        }
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // On s'assure qu'un admin ne peut éditer que les catégories générales
        if ($category->provenance_id !== null) {
            abort(403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)->whereNull('provenance_id')
            ],
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie mise à jour avec succès.');
    }
    
    public function destroy(Category $category)
    {
        // On s'assure qu'un admin ne peut supprimer que les catégories générales
        if ($category->provenance_id !== null) {
            abort(403);
        }
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                             ->with('error', 'Impossible de supprimer cette catégorie car des produits y sont liés.');
        }

        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie supprimée avec succès.');
    }

     public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }
}
