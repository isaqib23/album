<?php

namespace App\Http\Controllers;

use App\Entities\AlbumFriend;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\AlbumFriendRepository;

/**
 * Class AlbumFriendsController.
 *
 * @package namespace App\Http\Controllers;
 */
class AlbumFriendsController extends BaseController
{
    /**
     * @var AlbumFriendRepository
     */
    protected $repository;

    /**
     * AlbumFriendsController constructor.
     *
     * @param AlbumFriendRepository $repository
     */
    public function __construct(
        AlbumFriendRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function store(Request $request){
        $friends = $request->input('friends');
        $album_id = $request->input('album_id');

        $this->repository->store($album_id, $friends);

        return $this->sendResponse([],"Album Friends Added");
    }
}
