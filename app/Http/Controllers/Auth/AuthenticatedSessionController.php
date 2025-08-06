<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider; // Ajoutez cette ligne pour importer la classe RouteServiceProvider

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // 1. Vérification de l'état "is_active" et redirection conditionnelle
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Votre compte est en attente de validation par un administrateur.']);
        }
        
        // 2. Redirection dynamique basée sur le rôle, une fois le compte validé
        switch ($user->role->name) {
            case 'admin_principal':
            case 'superviseur_commercial':
            case 'superviseur_production':
                return redirect()->intended(route('admin.dashboard'));
            
            case 'client':
                return redirect()->intended(route('client.dashboard'));
            
            case 'partenaire':
                return redirect()->intended(route('partenaire.dashboard'));
            
            case 'chauffeur':
                return redirect()->intended(route('chauffeur.dashboard'));
            
            default:
                return redirect()->intended(RouteServiceProvider::HOME);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}