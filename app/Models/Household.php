<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Household extends Model
{
    /** @use HasFactory<\Database\Factories\HouseholdFactory> */
    use HasFactory, HasApiTokens;

    protected $guarded = ['password'];

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'mobile',
        'email',
        'primary_email',
        'password',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function validatePassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
}
