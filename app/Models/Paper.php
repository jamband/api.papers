<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $body
 * @property string $created_at
 * @property string $updated_at
 *
 * @method Builder byUserId(int $userId)
 */
class Paper extends Model
{
    use HasFactory;
    use PaperScope;

    public const CREATED_AT_FORMAT = 'M jS Y, g:i a';
    public const UPDATED_AT_FORMAT = self::CREATED_AT_FORMAT;

    public function getCreatedAtAttribute(mixed $value): string
    {
        return Carbon::parse($value)->format(self::CREATED_AT_FORMAT);
    }

    public function getUpdatedAtAttribute(mixed $value): string
    {
        return Carbon::parse($value)->format(self::UPDATED_AT_FORMAT);
    }
}
