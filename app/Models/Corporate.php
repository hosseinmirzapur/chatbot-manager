<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Corporate extends Model
{
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
}
