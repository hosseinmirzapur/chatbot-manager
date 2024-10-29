<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property Collection<Message> $messages
 * @property Corporate | Model $corporate
 * @property int $id
 * @property string $slug
 */
class Chat extends Model
{
    /**
     * @return BelongsTo
     */
    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

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
}
