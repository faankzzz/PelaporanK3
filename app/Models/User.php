<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'nip', 'role', 'unit_id', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public const ROLES = [
        'pelapor'    => 'Pelapor',
        'petugas_k3' => 'Petugas K3',
        'admin'      => 'Admin',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isK3(): bool { return $this->role === 'petugas_k3'; }
    public function isPelapor(): bool { return $this->role === 'pelapor'; }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
