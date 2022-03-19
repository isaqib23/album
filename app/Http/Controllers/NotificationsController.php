<?php

namespace App\Http\Controllers;

use App\Entities\Friend;
use App\Entities\Notification;
use App\Repositories\FriendRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\NotificationRepository;

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
        $response = Notification::orderBy("id","desc")->get();
        return $this->sendResponse($response,"Notification updated");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request){
        $request->validate([
            'status' => 'required|string',
            'notify_id' => 'required|string'
        ]);

        $result = $this->repository->update(["status" => $request->input("status")],$request->input("notify_id"));
        if($result->type == "friend_request"){
            Friend::where([
                "user_id"   => $result->receiver,
                "invited_by"   => $result->sender,
            ])->update(["status" => $request->input("status")]);
        }/*elseif ($result->type == "album_invitation"){

        }*/
        return $this->sendResponse($result,"Notification updated");
    }
}
