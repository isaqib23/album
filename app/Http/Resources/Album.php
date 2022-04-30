<?php

namespace App\Http\Resources;

use App\Entities\AlbumFriend;
use App\Entities\AlbumPost;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class Album extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (integer)$this->id,
            'name' => (string)$this->name,
            'cover_image' => (string)$this->cover_image,
            'created_by' => (integer)$this->created_by,
            'created_at' => (string)$this->created_at,
            'created_by_name' => (string)$this->createdBy->name,
            'post_count' => (integer)$this->postsCount($this),
            'friend_count' => $this->getFriends($this)->count(),
        ];
    }

    /**
     * @param $model
     * @return mixed
     */
    private function getFriends($model){
        $friendIds = AlbumFriend::where("album_id",$model->id)->get();

        return \App\Models\User::whereIn("id",$friendIds->pluck("user_id"))->get();
    }

    /**
     * @param $model
     * @return mixed
     */
    private function postsCount($model){
        return AlbumPost::where("album_id", $model->id)->count();
    }
}
