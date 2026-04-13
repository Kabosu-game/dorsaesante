<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalContent extends Model
{
    protected $fillable = [
        'title', 'content', 'type', 'category', 'thumbnail', 'media_url',
        'author_id', 'views_count', 'is_published', 'tags',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
