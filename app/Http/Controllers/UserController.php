<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        // Démarre une nouvelle requête Eloquent pour le modèle User,
        // en incluant la relation 'role' pour éviter les requêtes N+1.
        $query = User::with('role');

        // Filtrer par rôle :
        // Si le paramètre 'role_id' est présent dans la requête HTTP,
        // ajoute une condition WHERE pour filtrer les utilisateurs par ce rôle.
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtrer par statut (actif/inactif) :
        // Si le paramètre 'is_active' est présent,
        // ajoute une condition WHERE pour filtrer les utilisateurs par leur statut.
        // La valeur est convertie en booléen car 'is_active' est un champ booléen.
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        // Recherche par nom, prénom, email ou numéro de téléphone :
        // Si le paramètre 'search' est présent,
        // ajoute des conditions OR WHERE pour rechercher la chaîne dans plusieurs colonnes.
        // La recherche utilise 'like' avec des jokers (%) pour une correspondance partielle.
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        // Exécute la requête, pagine les résultats (10 par page)
        // et ajoute les paramètres de la requête actuelle à l'URL de pagination.
        $users = $query->paginate(10)->withQueryString();
        
        // Récupère tous les rôles pour les menus déroulants des filtres et modales.
        $roles = Role::all();

        // Retourne la vue 'users.index' en passant les utilisateurs paginés et tous les rôles.
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

        User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', false),
        ]);

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
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $userData = $request->except('password', 'password_confirmation');
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $userData['is_active'] = $request->boolean('is_active');

        $user->update($userData);

        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur mis à jour avec succès.');
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