<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class ProfileController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche le formulaire de modification du profil de l'utilisateur.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Met à jour les informations générales du profil de l'utilisateur.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $oldValues = $user->toArray(); // Capture des valeurs avant la mise à jour

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $user->update($validatedData);
        $newValues = $user->refresh()->toArray(); // Capture des nouvelles valeurs

        // Log de la mise à jour du profil
        $this->recordLog(
            'mise_a_jour_profil',
            'users',
            $user->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('profile.edit')->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    /**
     * Met à jour le mot de passe de l'utilisateur.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Le champ "Mot de passe actuel" est requis.',
            'password.required' => 'Le champ "Nouveau mot de passe" est requis.',
            'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            // Log de l'échec de la mise à jour du mot de passe
            $this->recordLog(
                'echec_mise_a_jour_mot_de_passe',
                'users',
                $user->id,
                ['error' => 'Mot de passe actuel incorrect'],
                null
            );
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Log de la mise à jour réussie du mot de passe
        $this->recordLog(
            'mise_a_jour_mot_de_passe',
            'users',
            $user->id,
            null,
            null // Pas de nouvelles valeurs à loguer ici
        );

        return redirect()->route('profile.edit')->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }
}