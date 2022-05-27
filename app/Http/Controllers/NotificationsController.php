<?php

namespace App\Http\Controllers;

use App\Entities\AlbumFriend;
use App\Entities\Friend;
use App\Entities\Notification;
use App\Repositories\FriendRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Validator;

/**
 * Class NotificationsController.
 *
 * @package namespace App\Http\Controllers;
 */
class NotificationsController extends BaseController
{
    /**
     * @var NotificationRepository
     */
    protected $repository;
    /**
     * @var FriendRepositoryEloquent
     */
    private $friendRepositoryEloquent;

    /**
     * NotificationsController constructor.
     *
     * @param NotificationRepository $repository
     * @param FriendRepositoryEloquent $friendRepositoryEloquent
     */
    public function __construct(
        NotificationRepository $repository,
        FriendRepositoryEloquent $friendRepositoryEloquent
    )
    {
        $this->repository = $repository;
        $this->friendRepositoryEloquent = $friendRepositoryEloquent;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request){
        $response = Notification::orderBy("id","desc")->where("status","sent")->get();
        return $this->sendResponse($response,"Notification updated");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'notify_id' => 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        $result = $this->repository->update(["status" => $request->input("status")],$request->input("notify_id"));
        if($result->type == "friend_request"){
            Friend::where([
                "user_id"   => $result->receiver,
                "invited_by"   => $result->sender,
            ])->update(["status" => $request->input("status")]);
        }elseif ($result->type == "album_invitation"){
            AlbumFriend::where([
                "user_id"   => $result->receiver,
                "album_id"  => $result->taggable_id
            ])->update(["status" => $request->input("status")]);
        }
        return $this->sendResponse($result,"Notification updated");
    }
}
