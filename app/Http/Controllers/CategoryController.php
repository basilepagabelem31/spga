<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class CategoryController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

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

        $categories = $query->paginate(8)->withQueryString();
        
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,provenance_id,NULL',
            'description' => 'nullable|string',
        ]);

        $validatedData['provenance_id'] = null;
        
        $category = Category::create($validatedData);

        // Ajout du log pour la création
        $this->recordLog(
            'creation_categorie',
            'categories',
            $category->id,
            null,
            $category->toArray()
        );

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category)
    {
        if ($category->provenance_id !== null) {
            abort(403);
        }
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if ($category->provenance_id !== null) {
            abort(403);
        }

        $oldValues = $category->toArray(); // Capture des valeurs avant la mise à jour

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

        // Ajout du log pour la mise à jour
        $newValues = $category->refresh()->toArray();
        $this->recordLog(
            'mise_a_jour_categorie',
            'categories',
            $category->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie mise à jour avec succès.');
    }
    
    public function destroy(Category $category)
    {
        if ($category->provenance_id !== null) {
            abort(403);
        }

        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                             ->with('error', 'Impossible de supprimer cette catégorie car des produits y sont liés.');
        }

        $oldValues = $category->toArray(); // Capture des valeurs avant la suppression
        $categoryId = $category->id;

        $category->delete();

        // Ajout du log pour la suppression
        $this->recordLog(
            'suppression_categorie',
            'categories',
            $categoryId,
            $oldValues,
            null
        );

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie supprimée avec succès.');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }
}