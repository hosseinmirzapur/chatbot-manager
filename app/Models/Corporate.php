<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property Collection<Chat> $chats
 * @property int $id
 * @property string $slug
 * @property string $status
 * @property string $api_key
 */
class Corporate extends Model
{
    use HasFactory;
    const STATUSES = [
        'PENDING', 'ACCEPTED', 'REJECTED'
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::lower(Str::random(10));
            }
        });
    }

    /**
     * @return HasMany
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
