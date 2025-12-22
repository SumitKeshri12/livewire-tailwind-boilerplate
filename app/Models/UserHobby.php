<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserHobby extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'user_id',
        'hobby',
    ];

    /**
     * Get the user that owns the hobby.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
