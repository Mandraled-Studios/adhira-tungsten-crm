<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    public function isAdmin() {
        return $this->role->value == 'Admin';
    }

    public function isDev() {
        return $this->role->value == 'Developer';
    }

    public function isAuditor() {
        return $this->role->value == 'Auditor';
    }

    public function isStaff() {
        return $this->role->value == 'Staff';
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, 
                                     Client::class, 
                                    'auditor_group_id',
                                    'client_id',
                                    'id',
                                    'id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '.com'); //&& $this->hasVerifiedEmail();
    }
}
