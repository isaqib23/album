<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\NotificationRepository;
use App\Entities\Notification;
use App\Validators\NotificationValidator;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class NotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationRepositoryEloquent extends BaseRepository implements NotificationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
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
     * @param $users
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function store($request, $users){
        foreach ($users as $user){
            Notification::create([
                "type"          => $request->input("type"),
                "sender"        => Auth::user()->id,
                "receiver"      => $user,
                "description"   => $request->input("description")
            ]);
        }
        return true;
    }
}
