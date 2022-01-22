<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public const CREATED_AT_FORMAT = 'M jS Y, g:i:s a';
    public const UPDATED_AT_FORMAT = self::CREATED_AT_FORMAT;
    public const EMAIL_VERIFIED_AT_FORMAT = self::CREATED_AT_FORMAT;

    public function getEmailVerifiedAtAttribute(mixed $value): string|null
    {
        if (null === $value) {
            return null;
        }

        return Carbon::parse($value)->format(self::EMAIL_VERIFIED_AT_FORMAT);
    }

    public function getCreatedAtAttribute(mixed $value): string
    {
        return Carbon::parse($value)->format(self::CREATED_AT_FORMAT);
    }

    public function getUpdatedAtAttribute(mixed $value): string
    {
        return Carbon::parse($value)->format(self::UPDATED_AT_FORMAT);
    }
}
