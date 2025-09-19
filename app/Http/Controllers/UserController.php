<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Partner;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class UserController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des utilisateurs avec leur rôle, et permet le filtrage/recherche.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('role')) {
            $roleNames = explode(',', $request->role);
            $query->whereHas('role', function($q) use ($roleNames) {
                $q->whereIn('name', $roleNames);
            });
        }
        
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

        $users = $query->paginate(8)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
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

        $role = Role::find($request->role_id);
        if ($role && $role->name === 'partenaire') {
            Partner::create([
                'user_id' => $user->id,
                'establishment_name' => $user->name . ' ' . $user->first_name,
                'type' => 'Producteur individuel',
            ]);
        }

        // Log de la création
        $this->recordLog(
            'creation_utilisateur',
            'users',
            $user->id,
            null,
            $user->toArray()
        );

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
        session()->flash('open_edit_modal_id', $user->id);

        $oldValues = $user->toArray(); // Capture des valeurs avant la mise à jour

        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|in:0,1',
        ]);
        
        $oldRoleId = $user->role_id;

        $userData = $request->except('password', 'password_confirmation');
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $userData['is_active'] = $request->boolean('is_active');
        
        $partnerRole = Role::where('name', 'partenaire')->first();

        DB::beginTransaction();
        try {
            $user->update($userData);
            
            if ($request->role_id == $partnerRole->id && $oldRoleId != $partnerRole->id) {
                if (!$user->partner) {
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
            elseif ($request->role_id != $partnerRole->id && $oldRoleId == $partnerRole->id) {
                if ($user->partner) {
                    $user->partner->delete();
                }
            }

            DB::commit();

            session()->forget('open_edit_modal_id');

            $newValues = $user->refresh()->toArray();
            $this->recordLog(
                'mise_a_jour_utilisateur',
                'users',
                $user->id,
                $oldValues,
                $newValues
            );

            return redirect()->route('users.index')
                             ->with('success', 'Utilisateur mis à jour avec succès. ✅');

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->recordLog(
                'echec_mise_a_jour_utilisateur',
                'users',
                $user->id,
                ['error' => 'Erreur de base de données', 'exception' => $e->getMessage()],
                $request->all()
            );

            return redirect()->route('users.index')
                             ->with('error', 'Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage() . ' ❌');
        }
    }

    /**
     * Supprime un utilisateur de la base de données.
     */
    public function destroy(User $user)
    {
        $oldValues = $user->toArray();
        $userId = $user->id;

        if ($user->id === auth()->user()->id) {
            $this->recordLog(
                'echec_suppression_utilisateur',
                'users',
                $userId,
                ['error' => 'Tentative de suppression de son propre compte'],
                null
            );
            return redirect()->route('users.index')->with('error', 'Impossible de supprimer votre propre compte.');
        }

        try {
            $user->delete();
            $this->recordLog(
                'suppression_utilisateur',
                'users',
                $userId,
                $oldValues,
                null
            );
            return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            $this->recordLog(
                'echec_suppression_utilisateur',
                'users',
                $userId,
                ['error' => 'Erreur de suppression', 'exception' => $e->getMessage()],
                null
            );
            return redirect()->route('users.index')->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }

    /**
     * Active le compte d'un utilisateur.
     */
    public function activate(User $user)
    {
        if (!$user->is_active) {
            $oldValues = $user->toArray();
            $user->update(['is_active' => true]);
            $newValues = $user->refresh()->toArray();

            $this->recordLog(
                'activation_compte_utilisateur',
                'users',
                $user->id,
                $oldValues,
                $newValues
            );
            return redirect()->route('users.index')->with('success', 'Compte utilisateur activé avec succès.');
        }
        
        $this->recordLog(
            'echec_activation_compte_utilisateur',
            'users',
            $user->id,
            ['error' => 'Compte déjà actif'],
            null
        );
        return redirect()->route('users.index')->with('info', 'Le compte utilisateur est déjà actif.');
    }

    /**
     * Désactive le compte d'un utilisateur.
     */
    public function deactivate(User $user)
    {
        if ($user->is_active) {
            if ($user->id === auth()->user()->id) {
                $this->recordLog(
                    'echec_desactivation_compte_utilisateur',
                    'users',
                    $user->id,
                    ['error' => 'Tentative de désactivation de son propre compte'],
                    null
                );
                return redirect()->route('users.index')->with('error', 'Impossible de désactiver votre propre compte.');
            }

            $oldValues = $user->toArray();
            $user->update(['is_active' => false]);
            $newValues = $user->refresh()->toArray();

            $this->recordLog(
                'desactivation_compte_utilisateur',
                'users',
                $user->id,
                $oldValues,
                $newValues
            );
            return redirect()->route('users.index')->with('success', 'Compte utilisateur désactivé avec succès.');
        }

        $this->recordLog(
            'echec_desactivation_compte_utilisateur',
            'users',
            $user->id,
            ['error' => 'Compte déjà inactif'],
            null
        );
        return redirect()->route('users.index')->with('info', 'Le compte utilisateur est déjà inactif.');
    }
}