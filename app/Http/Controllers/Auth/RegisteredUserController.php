<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Importez le modèle Role
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // --- Récupérer le rôle par défaut (par exemple, 'client') ---
        // Assurez-vous que ce rôle existe dans votre table `roles`
        $defaultRole = Role::where('name', 'client')->first();

        // Si le rôle 'client' n'existe pas, cela pourrait causer une erreur.
        // Vous devriez gérer ce cas, par exemple en lançant une exception ou en attribuant un autre rôle par défaut.
        if (!$defaultRole) {
            // Option 1: Gérer l'erreur ou un message pour l'administrateur
            // throw new \Exception('Le rôle "client" n\'existe pas dans la base de données.');
            // Option 2: Attribuer un rôle alternatif ou définir un ID par défaut connu
            $roleId = 1; // Ou tout autre ID de rôle valide si 'client' n'est pas trouvé
            // Idéalement, vous devriez avoir un RoleSeeder pour vous assurer que le rôle 'client' existe.
        } else {
            $roleId = $defaultRole->id;
        }


        $user = User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => false, // Définit à false par défaut pour la validation
            'role_id' => $roleId, // <-- Ajoutez cette ligne pour assigner le rôle
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}