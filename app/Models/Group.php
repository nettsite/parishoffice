<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use NettSite\Messenger\Models\Group as MessengerGroup;

/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property int|null $group_type_id
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read GroupDetail|null $detail
 * @property-read GroupType|null $groupType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Member> $members
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $leaders
 */
class Group extends MessengerGroup
{
    /**
     * Attributes that live in group_details, not in messenger_groups.
     * Intercepted by setAttribute/getAttribute and persisted via the saved event.
     */
    private const DETAIL_ATTRIBUTES = ['description', 'group_type_id', 'is_active'];

    protected array $detailData = [];

    protected static function booted(): void
    {
        static::saved(function (Group $group) {
            if (!empty($group->detailData)) {
                $group->detail()->updateOrCreate([], $group->detailData);
                $group->detailData = [];
                $group->unsetRelation('detail');
            } elseif ($group->wasRecentlyCreated) {
                // Ensure a detail record exists even when created without detail attributes
                $group->detail()->firstOrCreate([], ['is_active' => true]);
            }
        });
    }

    /**
     * Intercept writes to detail attributes so they are not persisted to messenger_groups.
     */
    public function setAttribute($key, $value): mixed
    {
        if (in_array($key, self::DETAIL_ATTRIBUTES, true)) {
            $this->detailData[$key] = $value;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Proxy reads for detail attributes through the detail relationship.
     */
    public function getAttribute($key): mixed
    {
        if (in_array($key, self::DETAIL_ATTRIBUTES, true)) {
            $detail = $this->relationLoaded('detail') ? $this->getRelation('detail') : $this->detail;

            return match ($key) {
                'is_active' => $detail?->is_active ?? true,
                default     => $detail?->{$key},
            };
        }

        return parent::getAttribute($key);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function detail(): HasOne
    {
        return $this->hasOne(GroupDetail::class, 'group_id');
    }

    /**
     * GroupType accessed through the group_details extension table.
     * Chain: messenger_groups.id → group_details.group_id → group_details.group_type_id → group_types.id
     */
    public function groupType(): HasOneThrough
    {
        return $this->hasOneThrough(
            GroupType::class,
            GroupDetail::class,
            'group_id',       // group_details FK → messenger_groups
            'id',             // group_types PK
            'id',             // messenger_groups local key
            'group_type_id',  // group_details FK → group_types
        );
    }

    /**
     * Members enrolled in this group for messenger delivery (via messenger_group_users).
     * Alias for the parent users() relationship.
     */
    public function members(): MorphToMany
    {
        return $this->users();
    }

    /**
     * Member membership with Matthew metadata (joined_at, is_active).
     */
    public function memberDetails(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'group_member')
                    ->withPivot(['joined_at', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Admin users who lead this group.
     */
    public function leaders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_leaders')
                    ->withPivot(['appointed_at'])
                    ->withTimestamps();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Enrol a member — writes to messenger_group_users (message delivery)
     * and group_member (Matthew membership metadata).
     */
    public function enrolMember(Member $member, array $attributes = []): void
    {
        $this->members()->syncWithoutDetaching([$member->id]);

        $this->memberDetails()->syncWithoutDetaching([
            $member->id => array_merge(['joined_at' => now()], $attributes),
        ]);
    }
}
