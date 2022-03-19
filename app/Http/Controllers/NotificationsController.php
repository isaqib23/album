<?php

namespace App\Http\Controllers;

use App\Entities\Notification;
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
     * NotificationsController constructor.
     *
     * @param NotificationRepository $repository
     */
    public function __construct(
        NotificationRepository $repository
    )
    {
        $this->repository = $repository;
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
        dd($result);
        return $this->sendResponse($result,"Notification updated");
    }
}
