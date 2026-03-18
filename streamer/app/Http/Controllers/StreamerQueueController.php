<?php
namespace App\Http\Controllers;

use App\Repositories\PublicRepository;
use App\Services\QueueService;
use App\Services\PublicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreamerQueueController extends Controller
{
    public function __construct(
        protected QueueService $queueService,
        protected PublicService $publicService
    ) {}

    public function show($scheduleId)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'vip_queues' => $this->queueService->getQueueVIP(),
                'public_queues' => $this->queueService->getQueuePublic($scheduleId),
            ]
        ]);
    }

    public function index2()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'live' => $this->queueService->getUnregisteredQueue(),
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,playing,completed,skipped']);
        $this->queueService->changeStatus($id, $request->status);
        
        return response()->json(['status' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function searchVip(Request $request)
    {
        $request->validate(['name' => 'required']);
        $data = (new PublicRepository)->searchVipByName($request->name, Auth::id());
        return response()->json(['status' => true, 'data' => $data]);
    }
}