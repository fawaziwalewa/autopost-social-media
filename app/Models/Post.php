<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasTags;

    protected $fillable = [
        'description',
        'image',
        'site_url',
        'is_posted',
        'is_posted_to_twitter',
        'is_posted_to_facebook',
        'published_at'
    ];
}
