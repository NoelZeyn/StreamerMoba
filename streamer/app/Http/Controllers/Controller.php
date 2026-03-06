<?php
namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function success($data = null, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }

    protected function created($data = null, string $message = 'Created'): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], Response::HTTP_CREATED);
    }

    protected function error(string $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }

    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function notFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }
}
