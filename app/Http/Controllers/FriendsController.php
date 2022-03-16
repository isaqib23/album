<?php

namespace App\Http\Controllers;

use App\Entities\Friend;
use App\Http\Resources\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\FriendRepository;
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
     * FriendsController constructor.
     *
     * @param FriendRepository $repository
     */
    public function __construct(
        FriendRepository $repository
    )
    {
        $this->repository = $repository;
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
            "user_id"       => $request->user_id,
            "invited_by"    => $request->user()->id
        ]);

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
            "invited_by"       => $request->user_id,
        ])->get();

        $friends = \App\Models\User::whereIn("id",$friendIds->pluck("user_id"))->get();

        return $this->sendResponse(User::collection($friends),"");
    }
}
