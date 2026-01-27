<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = ['title', 'user_id', 'is_completed', 'priority'];

    // This task belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
