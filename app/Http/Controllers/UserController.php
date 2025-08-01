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
     * Affiche la liste des utilisateurs.
     */
    public function index()
    {
        $users = User::with('role')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel utilisateur.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Stocke un nouvel utilisateur dans la base de données.
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
            'is_active' => 'boolean', // Peut être défini à false par défaut si en attente de validation
        ]);

        User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', false), // Défaut à false pour les nouveaux comptes nécessitant validation
            // 'email_verified_at' => now(), // Décommentez si vous voulez que les emails soient vérifiés à la création
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
     * Met à jour un utilisateur existant dans la base de données.
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