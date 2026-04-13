<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentalHealthResource extends Model
{
    protected $fillable = [
        'title', 'content', 'type', 'category', 'thumbnail', 'media_url',
        'author_id', 'requires_professional', 'is_published', 'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'requires_professional' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
