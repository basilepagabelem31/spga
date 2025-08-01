<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Cette ligne est cruciale


use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Les utilisateurs qui ont ce rôle.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Les permissions associées à ce rôle.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * Vérifie si le rôle possède une permission donnée.
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        return $this->permissions->contains('name', $permissionName);
    }
}
