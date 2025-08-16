<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
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
 */
class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory, HasApiTokens;

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
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
