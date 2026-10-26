<?php

namespace Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Modules\Support\Concerns\HasUuid;
use Spatie\Permission\Traits\HasRoles;
use Modules\User\Enums\UserStatuses;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;
    use HasUuid;
    
    protected $guarded = [];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected $appends = [
        'status_label',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'status' => UserStatuses::class,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === UserStatuses::ACTIVE;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    public function getStatusLabelAttribute(): ?string
    {
        return $this->status?->label();
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Storage::disk('public')->exists('users/' . $this->image)) {
            return asset('storage/users/' . $this->image);
        }
        
        return asset('assets/images/default-image.png');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%");
        });
    }
}
