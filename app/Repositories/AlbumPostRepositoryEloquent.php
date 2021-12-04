<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AlbumPostRepository;
use App\Entities\AlbumPost;
use App\Validators\AlbumPostValidator;

/**
 * Class AlbumPostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AlbumPostRepositoryEloquent extends BaseRepository implements AlbumPostRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AlbumPost::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $post_id
     * @param $friends
     * @return bool
     */
    public function store($post_id, $friends){
        AlbumPost::where("post_id", $post_id)->delete();

        $data = [];
        foreach ($friends as $value){
            array_push($data,[
                "post_id"  => $post_id,
                "album_id"   => $value,
                "created_at"    => date("Y-m-d H:i:s"),
                "updated_at"    => date("Y-m-d H:i:s")
            ]);
        }

        AlbumPost::insert($data);

        return true;
    }
}
