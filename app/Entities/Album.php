<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Album.
 *
 * @package namespace App\Entities;
 */
class Album extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "cover_image",
        "created_by"
    ];

    /**
     * @return BelongsTo
     */
    public function createdBy(){
        return $this->belongsTo(\App\Models\User::class,'created_by');
    }
}
