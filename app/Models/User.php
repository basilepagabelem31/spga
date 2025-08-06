<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'email',
        'password',
        'phone_number',
        'address',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
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
     * Le partenaire associé à cet utilisateur.
     */
    public function partner()
    {
        return $this->hasOne(Partner::class);
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
     * Vérifie si l'utilisateur est un administrateur ou un superviseur.
     */
    public function isAdmin(): bool
    {
        return $this->role && in_array($this->role->name, ['admin_principal', 'superviseur_commercial', 'superviseur_production']);
    }

    /**
     * Vérifie si l'utilisateur est un client.
     */
    public function isClient(): bool
    {
        return $this->role && $this->role->name === 'client';
    }

    /**
     * Vérifie si l'utilisateur est un partenaire.
     */
    public function isPartner(): bool
    {
        return $this->role && $this->role->name === 'partenaire';
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
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }


    /**
 * Vérifie si l'utilisateur possède l'un des rôles donnés.
 */
public function hasAnyRole(array $roles): bool
{
    return $this->role && in_array($this->role->name, $roles);
}

}