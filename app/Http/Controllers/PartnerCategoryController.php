<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class PartnerCategoryController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, LogsActivity; // Utilisation des traits

    public function index()
    {
        $partnerId = Auth::user()->partner->id;
        $categories = Category::where('provenance_id', $partnerId)->paginate(8);
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
                Rule::unique('categories')->where(fn ($query) => $query->where('provenance_id', $partnerId))
            ],
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'provenance_id' => $partnerId,
        ]);
        
        // Log de la création
        $this->recordLog(
            'creation_categorie_partenaire',
            'categories',
            $category->id,
            null,
            $category->toArray()
        );

        return redirect()->route('partenaire.categories.index')
                         ->with('success', 'Catégorie partenaire créée avec succès.');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('partners.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $oldValues = $category->toArray(); // Capture des valeurs avant la mise à jour
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
        $newValues = $category->refresh()->toArray(); // Capture des nouvelles valeurs
        
        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_categorie_partenaire',
            'categories',
            $category->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('partenaire.categories.index')
                         ->with('success', 'Catégorie partenaire mise à jour avec succès.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        if ($category->products()->count() > 0) {
            // Log de la tentative de suppression infructueuse
            $this->recordLog(
                'echec_suppression_categorie',
                'categories',
                $category->id,
                ['error' => 'Catégorie liée à des produits'],
                null
            );

            return redirect()->route('partenaire.categories.index')
                             ->with('error', 'Impossible de supprimer cette catégorie car des produits y sont liés.');
        }

        $oldValues = $category->toArray(); // Capture des valeurs avant la suppression
        $categoryId = $category->id;

        $category->delete();
        
        // Log de la suppression
        $this->recordLog(
            'suppression_categorie_partenaire',
            'categories',
            $categoryId,
            $oldValues,
            null
        );

        return redirect()->route('partenaire.categories.index')
                         ->with('success', 'Catégorie partenaire supprimée avec succès.');
    }
}