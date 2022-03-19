<?php

namespace App\Http\Controllers;

use App\Entities\Friend;
use App\Http\Resources\User;
use App\Repositories\NotificationRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\FriendRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class FriendsController.
 *
 * @package namespace App\Http\Controllers;
 */
class FriendsController extends BaseController
{
    /**
     * @var FriendRepository
     */
    protected $repository;
    /**
     * @var NotificationRepositoryEloquent
     */
    private $notificationRepositoryEloquent;

    /**
     * FriendsController constructor.
     *
     * @param FriendRepository $repository
     * @param NotificationRepositoryEloquent $notificationRepositoryEloquent
     */
    public function __construct(
        FriendRepository $repository,
        NotificationRepositoryEloquent $notificationRepositoryEloquent
    )
    {
        $this->repository = $repository;
        $this->notificationRepositoryEloquent = $notificationRepositoryEloquent;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendInvite(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $this->repository->create([
            "user_id"       => $request->input("user_id"),
            "invited_by"    => Auth::user()->id
        ]);

        // send Notification
        $request->merge([
            "type"          => "friend_request",
            "description"   => Auth::user()->first_name." invited you as friend"
        ]);

        $this->notificationRepositoryEloquent->store($request,[$request->user_id]);

        return $this->sendResponse([],"User Invited Successfully!");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function inviteStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'status'    => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        Friend::where([
            "user_id"       => $request->user()->id,
            "invited_by"    => $request->user_id,
        ])->update(["status" => $request->status]);

        return $this->sendResponse([],"Inviitation ".ucfirst($request->status)." Successfully!");
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return $this->sendResponse([],"Friend Deleted");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function myFriends(Request $request){
        $friendIds = Friend::where([
            "invited_by"       => Auth::user()->id,
        ])->orWhere([
            "user_id"   => Auth::user()->id
        ])->get();

        $friends = [];
        if($friendIds->count() > 0){
            $users = $friendIds->pluck("user_id");
            if(Auth::user()->id == $users[0]){
                $friends = \App\Models\User::whereIn("id",$friendIds->pluck("user_id"))->get();
            }else{
                $friends = \App\Models\User::whereIn("id",$friendIds->pluck("invited_by"))->get();
            }
        }

        return $this->sendResponse(User::collection($friends),"");
    }
}
