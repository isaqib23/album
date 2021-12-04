<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PostLikeRepository;
use App\Entities\PostLike;
use App\Validators\PostLikeValidator;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PostLikeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PostLikeRepositoryEloquent extends BaseRepository implements PostLikeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PostLike::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws ValidatorException
     */
    public function store($request){
        return $this->updateOrCreate([
                    "post_id"   => $request->input("post_id"),
                    "user_id"   => auth()->user()->id
                ],[
                    "post_id"   => $request->input("post_id"),
                    "user_id"   => auth()->user()->id
                ]);
    }
}
