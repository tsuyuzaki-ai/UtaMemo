<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repertoire extends Model
{
    protected $fillable = [
        'user_id',
        'track_id',
        'title',
        'artist',
        'album_image',
        'is_favorite',
        'skill_level',
        'key'
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'skill_level' => 'integer',
        'key' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
