<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'active',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasPanelShield;
    use HasRoles;
    use Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        // Accede chi è attivo e ha almeno un ruolo (admin, teacher, ecc.).
        return (bool) $this->active && $this->roles()->exists();
    }

    /**
     * Utente limitato all'ambito scuola: ha il ruolo teacher
     * e nessun ruolo che gli dia accesso alle finanze generali.
     */
    public function isSchoolOnly(): bool
    {
        return $this->hasRole('teacher')
            && ! $this->hasAnyRole(['super_admin', 'admin', 'treasurer']);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }
}
