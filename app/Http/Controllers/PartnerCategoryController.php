<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class PartnerCategoryController extends Controller
{

    use AuthorizesRequests, ValidatesRequests;
    
    public function index()
    {
        $partnerId = Auth::user()->partner->id;
        $categories = Category::where('provenance_id', $partnerId)->paginate(10);
        return view('partners.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('partners.categories.create');
    }

    public function store(Request $request)
    {
        $partnerId = Auth::user()->partner->id;

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // La validation d'unicité prend en compte l'ID du partenaire
                Rule::unique('categories')->where(fn ($query) => $query->where('provenance_id', $partnerId))
            ],
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'provenance_id' => $partnerId,
        ]);

        return redirect()->route('partenaire.categories.index')
                         ->with('success', 'Catégorie partenaire créée avec succès.');
    }

    public function edit(Category $category)
    {
        // S'assurer que le partenaire ne peut éditer que ses propres catégories
        $this->authorize('update', $category);
        return view('partners.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // S'assurer que le partenaire ne peut modifier que ses propres catégories
        $this->authorize('update', $category);

        $partnerId = Auth::user()->partner->id;
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)->where(fn ($query) => $query->where('provenance_id', $partnerId))
            ],
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('partenaire.categories.index')
                         ->with('success', 'Catégorie partenaire mise à jour avec succès.');
    }

    public function destroy(Category $category)
    {
        // S'assurer que le partenaire ne peut supprimer que ses propres catégories
        $this->authorize('delete', $category);

        if ($category->products()->count() > 0) {
            return redirect()->route('partenaire.categories.index')
                             ->with('error', 'Impossible de supprimer cette catégorie car des produits y sont liés.');
        }

        $category->delete();

        return redirect()->route('partenaire.categories.index')
                         ->with('success', 'Catégorie partenaire supprimée avec succès.');
    }
}