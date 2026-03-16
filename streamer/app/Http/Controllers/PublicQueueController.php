<?php

namespace App\Http\Controllers;

use App\Services\PublicService;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicQueueController extends Controller
{
    public function __construct(
        protected PublicService $publicService,
    ) {}
    public function getStreamers()
    {
        $data = $this->publicService->getStreamerList();
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function getSchedules(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $data = $this->publicService->getSchedulesForStreamer($request->user_id);
        return response()->json(['status' => true, 'data' => $data]);
    }
    public function getQueueItems(Request $request)
    {
        $request->validate(['schedule_id' => 'required|exists:stream_schedules,id']);

        $data = $this->publicService->getQueueList($request->schedule_id);
        return response()->json(['status' => true, 'data' => $data]);
    }
}
