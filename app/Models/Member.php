<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $id_number
 * @property \Carbon\Carbon|null $date_of_birth
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $password
 * @property bool $baptised
 * @property \Carbon\Carbon|null $baptism_date
 * @property string|null $baptism_parish
 * @property bool $first_communion
 * @property \Carbon\Carbon|null $first_communion_date
 * @property string|null $first_communion_parish
 * @property bool $confirmed
 * @property \Carbon\Carbon|null $confirmation_date
 * @property string|null $confirmation_parish
 * @property int $household_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read string $full_name
 * @property-read Household $household
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $groups
 */
class Member extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasApiTokens, HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'baptised' => 'boolean',
        'first_communion' => 'boolean',
        'confirmed' => 'boolean',
        'baptism_date' => 'date',
        'first_communion_date' => 'date',
        'confirmation_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_member')
                    ->withPivot(['joined_at', 'is_active'])
                    ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('baptism_certificates')
            ->acceptsMimeTypes(['application/pdf', 'application/octet-stream', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('first_communion_certificates')
            ->acceptsMimeTypes(['application/pdf', 'application/octet-stream', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('confirmation_certificates')
            ->acceptsMimeTypes(['application/pdf', 'application/octet-stream', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Remove all conversions to eliminate potential issues
        // Images will be displayed at their original size
    }
}
