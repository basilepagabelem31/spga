<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification; // Ajoutez ceci en haut du fichier avec les autres 'use'


class User extends Authenticatable implements MustVerifyEmail // Implémente MustVerifyEmail si l'email est vérifié
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name', // Ajouté
        'email',
        'password',
        'phone_number', // Ajouté
        'address',      // Ajouté
        'role_id',      // Ajouté
        'is_active',    // Ajouté
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean', // Ajouté
        'password' => 'hashed',
    ];

    /**
     * Le rôle de l'utilisateur.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Les partenaires créés par cet utilisateur.
     */
    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    /**
     * Les commandes passées par cet utilisateur (en tant que client).
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * Les commandes validées par cet utilisateur.
     */
    public function validatedOrders()
    {
        return $this->hasMany(Order::class, 'validated_by');
    }

    /**
     * Les tournées de livraison conduites par cet utilisateur (en tant que chauffeur).
     */
    public function deliveryRoutes()
    {
        return $this->hasMany(DeliveryRoute::class, 'driver_id');
    }

    /**
     * Les contrôles qualité effectués par cet utilisateur.
     */
    public function qualityControls()
    {
        return $this->hasMany(QualityControl::class, 'controller_id');
    }

    /**
     * Les non-conformités dont la décision a été prise par cet utilisateur.
     */
    public function decidedNonConformities()
    {
        return $this->hasMany(NonConformity::class, 'decision_taken_by');
    }

    /**
     * Les notifications reçues par cet utilisateur.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Les logs d'activité de cet utilisateur.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Accesseur pour le nom complet de l'utilisateur.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->first_name}";
    }

    /**
     * Vérifie si l'utilisateur est un administrateur principal.
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin_principal';
    }

    /**
     * Vérifie si l'utilisateur est un client.
     */
    public function isClient(): bool
    {
        return $this->role && $this->role->name === 'client';
    }

    /**
     * Vérifie si l'utilisateur est un partenaire stratégique.
     */
    public function isPartner(): bool
    {
        return $this->role && $this->role->name === 'partenaire_strategique';
    }

    /**
     * Vérifie si l'utilisateur est un chauffeur.
     */
    public function isDriver(): bool
    {
        return $this->role && $this->role->name === 'chauffeur';
    }

    /**
     * Vérifie si l'utilisateur est un superviseur commercial.
     */
    public function isSupervisorCommercial(): bool
    {
        return $this->role && $this->role->name === 'superviseur_commercial';
    }

    /**
     * Vérifie si l'utilisateur est un superviseur de production.
     */
    public function isSupervisorProduction(): bool
    {
        return $this->role && $this->role->name === 'superviseur_production';
    }

    /**
     * Vérifie si le compte utilisateur est actif.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }




    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

}