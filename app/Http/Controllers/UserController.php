<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Partner; // N'oubliez pas d'importer le modèle Partner
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs avec leur rôle.
     * Cette méthode sera utilisée par les administrateurs pour la gestion des comptes.
     */
     /**
     * Affiche la liste des utilisateurs avec leur rôle.
     */
   /**
     * Affiche la liste des utilisateurs avec leur rôle, et permet le filtrage/recherche.
     * Cette méthode sera utilisée par les administrateurs pour la gestion des comptes.
     */
   public function index(Request $request)
    {
        $query = User::with('role');

        // Filtrer par rôle(s) si le paramètre 'role' est présent dans l'URL.
        // C'est cette condition qui gère les nouvelles routes spécifiques.
        if ($request->filled('role')) {
            $roleNames = explode(',', $request->role);
            $query->whereHas('role', function($q) use ($roleNames) {
                $q->whereIn('name', $roleNames);
            });
        }
        
        // Gérer le filtre par ID de rôle pour la vue
        elseif ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel utilisateur.
     * Les rôles sont chargés pour être affichés dans un menu déroulant.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Stocke un nouvel utilisateur dans la base de données après validation.
     */
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'role_id' => 'required|exists:roles,id',
        'is_active' => 'boolean',
    ]);

    $user = User::create([
        'name' => $request->name,
        'first_name' => $request->first_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'address' => $request->address,
        'role_id' => $request->role_id,
        'is_active' => $request->boolean('is_active', false),
    ]);

    // ✅ Si rôle partenaire → créer une ligne partner
    $role = Role::find($request->role_id);
    if ($role && $role->name === 'partenaire') {
        \App\Models\Partner::create([
            'user_id' => $user->id,
            'establishment_name' => $user->name . ' ' . $user->first_name,
            'type' => 'Producteur individuel', // valeur par défaut
        ]);
    }

    return redirect()->route('users.index')
                     ->with('success', 'Utilisateur créé avec succès.');
}


    /**
     * Affiche les détails d'un utilisateur spécifique.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur existant dans la base de données après validation.
     */
  public function update(Request $request, User $user)

{
    // Stocke l'ID de l'utilisateur dans la session pour rouvrir le bon modal si la validation échoue
    session()->flash('open_edit_modal_id', $user->id);

    // 1. Validation des données de la requête
    $request->validate([
        'name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'password' => 'nullable|string|min:8|confirmed',
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'role_id' => 'required|exists:roles,id',
        'is_active' => 'nullable|in:0,1', // <-- Correction ici !
    ]);
    
    // Récupère l'ID de l'ancien rôle de l'utilisateur AVANT la mise à jour
    $oldRoleId = $user->role_id;

    // 2. Préparation des données pour la mise à jour
    $userData = $request->except('password', 'password_confirmation');
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->password);
    }
    $userData['is_active'] = $request->boolean('is_active');
    
    // Obtenez l'objet Role pour 'partenaire'
    $partnerRole = Role::where('name', 'partenaire')->first();

    // 3. Exécution de la mise à jour dans une transaction
    DB::beginTransaction();
    try {
        // Effectue la mise à jour de l'utilisateur
        $user->update($userData);
        
        // Logique de gestion du partenaire basée sur le changement de rôle
        // Condition pour passer à un rôle de partenaire
        if ($request->role_id == $partnerRole->id && $oldRoleId != $partnerRole->id) {
            if (!$user->partner) {
                // Crée un nouvel enregistrement Partner si l'utilisateur n'en a pas déjà un
                Partner::create([
                    'user_id' => $user->id,
                    'establishment_name' => $user->name . ' ' . $user->first_name,
                    'type' => 'Producteur individuel',
                    'contact_person' => $user->first_name . ' ' . $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                ]);
            }
        } 
        // Condition pour quitter le rôle de partenaire
        elseif ($request->role_id != $partnerRole->id && $oldRoleId == $partnerRole->id) {
            if ($user->partner) {
                // Supprime l'enregistrement Partner associé
                $user->partner->delete();
            }
        }

        DB::commit(); // Validez la transaction si tout s'est bien passé

        // 4. Supprime la session pour que le modal ne se rouvre pas après un succès
        session()->forget('open_edit_modal_id');

        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur mis à jour avec succès. ✅');

    } catch (\Exception $e) {
        DB::rollBack(); // Annulez la transaction en cas d'erreur
        
        return redirect()->route('users.index')
                         ->with('error', 'Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage() . ' ❌');
    }
}
 /**
     * Supprime un utilisateur de la base de données.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Active le compte d'un utilisateur.
     */
    public function activate(User $user)
    {
        if (!$user->is_active) {
            $user->update(['is_active' => true]);
            return redirect()->route('users.index')->with('success', 'Compte utilisateur activé avec succès.');
        }
        return redirect()->route('users.index')->with('info', 'Le compte utilisateur est déjà actif.');
    }

    /**
     * Désactive le compte d'un utilisateur.
     */
    public function deactivate(User $user)
    {
        if ($user->is_active) {
            $user->update(['is_active' => false]);
            return redirect()->route('users.index')->with('success', 'Compte utilisateur désactivé avec succès.');
        }
        return redirect()->route('users.index')->with('info', 'Le compte utilisateur est déjà inactif.');
    }
}