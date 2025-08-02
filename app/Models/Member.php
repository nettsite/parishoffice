<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

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
