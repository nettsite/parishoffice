<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $group_type_id
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Member> $members
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $leaders
 * @property-read GroupType|null $groupType
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'group_type_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'group_member')
                    ->withPivot(['joined_at', 'is_active'])
                    ->withTimestamps();
    }

    public function leaders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_leaders')
                    ->withPivot(['appointed_at'])
                    ->withTimestamps();
    }

    public function groupType(): BelongsTo
    {
        return $this->belongsTo(GroupType::class);
    }
}
