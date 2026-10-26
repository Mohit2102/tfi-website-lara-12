<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'city',
        'gender',
        'date_of_birth',
        'bio',
        'avatar',
        'trainer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => '/superadmin/dashboard',
            self::ROLE_ADMIN      => '/admin/dashboard',
            default               => '/customer/dashboard',
        };
    }

    public function getRoleBadgeColor(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => '#7c3aed',
            self::ROLE_ADMIN      => '#0ea5e9',
            default               => '#10b981',
        };
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'trainer_id');
    }

    public function assignedTrainees(): HasMany
    {
        return $this->hasMany(self::class, 'trainer_id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function trainees(): HasMany
    {
        return $this->hasMany(UserPlan::class, 'trainer_id');
    }

    public function avatarUrl(): ?string
    {
        if (! filled($this->avatar)) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return asset($this->avatar);
    }
}
