<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicQueueRequest;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;

class QueueController extends Controller
{
    public function __construct(
        protected QueueService $queueService,
    ) {}

    public function store(PublicQueueRequest $request): JsonResponse
    {
        try {
            $queue = $this->queueService->joinPublicQueue($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil masuk antrean!',
                'data'    => [
                    'nickname'     => $queue->nickname,
                    'queue_number' => $queue->queue_number,
                    'status'       => $queue->status
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
