<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AlbumFriendRepository;
use App\Entities\AlbumFriend;
use App\Validators\AlbumFriendValidator;

/**
 * Class AlbumFriendRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AlbumFriendRepositoryEloquent extends BaseRepository implements AlbumFriendRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AlbumFriend::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $album_id
     * @param $friends
     * @return bool
     */
    public function store($album_id, $friends){
        AlbumFriend::where("album_id", $album_id)->delete();

        $data = [];
        foreach ($friends as $value){
            array_push($data,[
                "album_id"  => $album_id,
                "user_id"   => $value,
                "created_at"    => date("Y-m-d H:i:s"),
                "updated_at"    => date("Y-m-d H:i:s")
            ]);
        }

        AlbumFriend::insert($data);

        return true;
    }
}
