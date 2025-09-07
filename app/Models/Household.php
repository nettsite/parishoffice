<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use App\Notifications\HouseholdResetPassword;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $email
 * @property string|null $primary_email
 * @property string $password
 * @property \Carbon\Carbon|null $terms_accepted
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Member> $members
 */
class Household extends Model implements CanResetPassword
{
    /** @use HasFactory<\Database\Factories\HouseholdFactory> */
    use HasFactory, HasApiTokens, Notifiable;

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
        'terms_accepted',
    ];

    protected $casts = [
        'terms_accepted' => 'datetime',
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

    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function sendPasswordResetNotification($token, $resetUrl = null)
    {
        $this->notify(new HouseholdResetPassword($token, $resetUrl));
    }
}
