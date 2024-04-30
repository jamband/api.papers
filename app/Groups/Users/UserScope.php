<?php

declare(strict_types=1);

namespace App\Groups\Users;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self byEmail(string $email)
 * @see self::scopeByEmail()
 */
trait UserScope
{
    /**
     * @param Builder<self> $query
     * @param string $email
     * @return Builder<self>
     */
    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }
}
