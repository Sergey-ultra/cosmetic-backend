<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property int id
 * @property string name
 * @property string email
 * @property string password
 * @property int role_id
 * @property int balance
 * @property int referral_balance
 * @property string|null service
 * @property string|null service_user_id
 * @property string|null ref
 * @property int|null referral_owner
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 *
 * @property-read string|null remember_token
 * @property-read float balanceNormal
 * @property-read float referralBalanceNormal
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    public const ROLE_ADMIN = 1;
    public const ROLE_MODERATOR = 2;
    public const ROLE_CLIENT = 3;
    public const ROLE_WRITER = 4;
    public const ROLE_SUPPLIER= 5;
    public const ROLE_BOT = 6;

    public const REVIEW_EDITOR = 7;

    public const ROLE_MAP_NAME = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_MODERATOR => 'Moderator',
        self::ROLE_CLIENT => 'Client',
        self::ROLE_WRITER => 'Writer',
        self::ROLE_SUPPLIER => 'Supplier',
        self::ROLE_BOT => 'Bot',
        self::REVIEW_EDITOR => 'Review_editor'
    ];

    public const TABLE = 'users';

    protected $table = self::TABLE;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'balance',
        'referral_balance',
        'service',
        'service_user_id',
        'ref',
        'referral_owner',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'  => 'date:Y-m-d',
    ];

    /**
     * Хранится в тысячных рубля
     * @return Attribute
     */
    protected function balanceNormal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => (float)$attributes['balance'] / 1000
        );
    }

    /**
     * Хранится в тысячных рубля
     * @return Attribute
     */
    protected function referralBalanceNormal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => (float)$attributes['referral_balance'] / 1000
        );
    }



    public function role(): string
    {
        return self::ROLE_MAP_NAME[$this->role_id];
    }

    public function hasAnyRole($roles): bool
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if (strtolower($role) === strtolower($this->role())) {
                return true;
            }
        }

        return false;
    }

    public function getBearerToken(): ?string
    {
        $tokenObject = $this->createToken('authToken');
        if ($tokenObject instanceof NewAccessToken) {
            return $tokenObject->plainTextToken;
        } elseif ($tokenObject instanceof PersonalAccessTokenResult) {
            return $tokenObject->accessToken;
        }
        return null;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(UserMessage::class, 'to_user', 'id');
    }

    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    public function telegramInfo(): HasOne
    {
        return $this->hasOne(UserTelegramInfo::class);
    }

    public function moneyCharges(): HasMany
    {
        return $this->hasMany(UserBalanceCharge::class);
    }
}
