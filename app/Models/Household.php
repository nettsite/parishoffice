<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Household extends Model
{
    /** @use HasFactory<\Database\Factories\HouseholdFactory> */
    use HasFactory;

    protected $guarded = [];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
