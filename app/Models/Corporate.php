<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property Collection<Chat> $chats
 */
class Corporate extends Model
{
    use HasFactory;
    const STATUSES = [
        'PENDING', 'ACCEPTED', 'REJECTED'
    ];

    protected $hidden = ['api_key'];

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
