<?php

namespace App\Http\Controllers;

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
    public function updateStatus(Request $request){
        $request->validate([
            'status' => 'required|string',
            'notify_id' => 'required|string'
        ]);

        $result = $this->repository->update(["type" => $request->input("status")],$request->input("notify_id"));

        return $this->sendResponse($result,"Notification updated");
    }
}
