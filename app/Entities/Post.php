<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Laravelista\Comments\Commentable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Tags\HasTags;

/**
 * Class Post.
 *
 * @package namespace App\Entities;
 */
class Post extends Model implements Transformable
{
    use TransformableTrait, HasTags, Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "caption",
        "created_by"
    ];

    /**
     * @return BelongsTo
     */
    public function createdBy(){
        return $this->belongsTo(\App\Models\User::class,'created_by');
    }

    public function scopePopularTags()
    {
        return DB::table('taggables')
            ->selectRaw('name,slug, count(tag_id) as tagged_count')
            ->join('tags', 'tags.id', '=', 'taggables.tag_id')
            ->groupBy('tags.id')
            ->orderBy('tagged_count', 'desc')
            ->get()

            ->map(function($tag){
                return [
                    'name' => json_decode($tag->name)->es,
                    'slug' => json_decode($tag->slug)->es,
                    'count' => $tag->tagged_count
                ];
            });
    }
}
