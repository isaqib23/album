<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\InviteFriendRepository;
use App\Entities\InviteFriend;
use App\Validators\InviteFriendValidator;

/**
 * Class InviteFriendRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class InviteFriendRepositoryEloquent extends BaseRepository implements InviteFriendRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return InviteFriend::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
