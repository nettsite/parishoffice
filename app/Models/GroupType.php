<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasPermissions;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $groups
 */
class GroupType extends Model
{
    use HasFactory, HasPermissions;

    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $guard_name = 'web';

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
