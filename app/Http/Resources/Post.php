<?php

namespace App\Http\Resources;

use App\Entities\AlbumPost;
use App\Entities\PostImage;
use App\Entities\PostLike;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravelista\Comments\Comment;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (integer)$this->id,
            'caption' => (string)$this->caption,
            'images' => $this->getImages($this),
            'likes' => (int)$this->postLikesCount($this),
            'comment_count' => (string)$this->postCommentsCount($this),
            'like_by_me' => (boolean)$this->likeByMe($this),
            'tags' => $this->tagsTransform($this),
            'albums' => $this->getAlbums($this),
            'created_by' => (integer)$this->created_by,
            'created_by_name' => (string)$this->createdBy->name,
            'created_by_photo' => (string)$this->createdBy->photo,
            'created_at' => (string)$this->created_at
        ];
    }

    /**
     * @param $model
     * @return mixed
     */
    private function getImages($model){
        return PostImage::where("post_id",$model->id)->select("id","image")->get();
    }

    /**
     * @param $model
     * @return array
     */
    private function tagsTransform($model)
    {
        $tags = [];
        if (!is_null($model->tags)) {
            foreach ($model->tags as $key => $value) {
                $tags[] = [
                    'id'    => $value->id,
                    'name'    => $value->name,
                    'slug'    => $value->slug
                ];
            }
        }
        return $tags;
    }

    /**
     * @param $model
     * @return mixed
     */
    private function getAlbums($model){
        $albumsIds = AlbumPost::where("post_Id",$model->id)->get();

        $albums =  \App\Entities\Album::whereIn("id",$albumsIds->pluck("album_id")->toArray())->get();

        return Album::collection($albums);
    }

    /**
     * @param $model
     * @return mixed
     */
    private function postLikesCount($model){
        return PostLike::where("post_id", $model->id)->count();
    }

    /**
     * @param $model
     * @return bool
     */
    private function likeByMe($model){
        $getLike = PostLike::where([
            "post_id"   => $model->id,
            "user_id"   => auth()->user()->id
        ])->first();
        return ($getLike) ? true : false;
    }

    /**
     * @param $model
     * @return mixed
     */
    private function postCommentsCount($model){
        return Comment::where("commentable_id", $model->id)->count();
    }
}
