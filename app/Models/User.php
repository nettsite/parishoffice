<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanResetPassword, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    /**
     * Determine if the user can access the given Filament panel.
     *
     * @param  Panel  $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Allow all users to access the panel and receive password reset emails
    }

    public function ledGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_leaders')
                    ->withPivot(['appointed_at'])
                    ->withTimestamps();
    }

    public function canAccessGroup(Group $group): bool
    {
        return $this->hasRole('Administrator') || $this->ledGroups->contains($group);
    }

    public function hasGroupTypePermission(string $permission, Group $group): bool
    {
        if ($this->hasRole('Administrator')) {
            return true;
        }

        if (!$this->canAccessGroup($group)) {
            return false;
        }

        return $group->groupType?->hasPermissionTo($permission) ?? false;
    }

    public function hasPermissionForMember(string $permission, Member $member): bool
    {
        if ($this->hasRole('Administrator')) {
            return true;
        }

        // Check if user has this permission through any group they lead that contains this member
        return $this->ledGroups()
            ->whereHas('members', fn($q) => $q->where('members.id', $member->id))
            ->whereHas('groupType.permissions', fn($q) => $q->where('name', $permission))
            ->exists();
    }
}
