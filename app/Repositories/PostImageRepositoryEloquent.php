<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PostImageRepository;
use App\Entities\PostImage;
use App\Validators\PostImageValidator;

/**
 * Class PostImageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PostImageRepositoryEloquent extends BaseRepository implements PostImageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PostImage::class;
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
     * @param $images
     * @return bool
     */
    public function store($post_id, $images, $isUpdate = false){
        if(!$isUpdate) {
            PostImage::where("post_id", $post_id)->delete();
        }

        $data = [];
        foreach ($images as $value){
            array_push($data,[
                "post_id"  => $post_id,
                "image"   => $value,
                "created_at"    => date("Y-m-d H:i:s"),
                "updated_at"    => date("Y-m-d H:i:s")
            ]);
        }

        PostImage::insert($data);

        return true;
    }
}
