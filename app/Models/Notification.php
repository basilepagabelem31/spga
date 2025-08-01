<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * L'utilisateur qui reçoit la notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marque la notification comme lue.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Vérifie si la notification a été lue.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }
}