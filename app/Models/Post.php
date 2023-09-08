<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_PUBLISHED = 'PUBLISHED';

    const TYPE_POST = 'post';
    const TYPE_PAGE = 'page';

    public function scopeExcludeFeatured($query)
    {
        $featured = Post::where('featured', 1)->where('type', 'post')->first();
        $query->where('id', '!=', ($featured->id) ?? 0);
    }

    public function scopeTypePost($query)
    {
        $query->where('type', 'post');
    }

    public function scopePublished($query)
    {
        $query->where('status', Post::STATUS_PUBLISHED);
    }

    public function scopeList($query)
    {
        return $query->orderBy('created_at', 'DESC')
            ->excludeFeatured()
            ->typePost()
            ->published();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
