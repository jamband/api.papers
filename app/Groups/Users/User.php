<?php

declare(strict_types=1);

namespace App\Groups\Users;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 *
 * @mixin Builder<self>
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use UserScope;

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

    public function emailVerifiedAt(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value): string|null => null === $value
                ? null
                : Carbon::parse($value)->format(self::EMAIL_VERIFIED_AT_FORMAT),
        );
    }

    public function createdAt(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value): string => Carbon::parse($value)->format(self::CREATED_AT_FORMAT),
        );
    }

    public function updatedAt(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value): string => Carbon::parse($value)->format(self::UPDATED_AT_FORMAT),
        );
    }
}
