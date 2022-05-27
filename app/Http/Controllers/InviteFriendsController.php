<?php

namespace App\Http\Controllers;

use App\Entities\InviteFriend;
use App\Mail\InviteUserEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\InviteFriendRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Str;

/**
 * Class InviteFriendsController.
 *
 * @package namespace App\Http\Controllers;
 */
class InviteFriendsController extends BaseController
{
    /**
     * @var InviteFriendRepository
     */
    protected $repository;

    /**
     * InviteFriendsController constructor.
     *
     * @param InviteFriendRepository $repository
     */
    public function __construct(
        InviteFriendRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function process_invites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        if (InviteFriend::where('email', $request->input('email'))->exists()) {
            return $this->sendError('User already invited with this email!');
        }

        if ($validator->fails()) {
            return $this->sendError('User already exist with this email');
        }

        do {
            $token = \Illuminate\Support\Str::random(20);
        } while (InviteFriend::where('token', $token)->first());
        InviteFriend::create([
            'token' => $token,
            'email' => $request->input('email')
        ]);

        // Send Confirmation Email
        $emailBody = [
            "name"      => $request->user()->name
        ];
        Mail::to($request->input('email'))->send(new InviteUserEmail($emailBody));

        return $this->sendResponse([],"The Invite has been sent successfully");
    }
}
