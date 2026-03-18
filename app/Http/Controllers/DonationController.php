<?php
namespace App\Http\Controllers;

use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Donation;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    public function index(): JsonResponse
    {
        $userId = Auth::id();
        $donations = $this->donationService->getDonationHistory($userId);

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    public function show($id): JsonResponse
    {
        $donation = Donation::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $donation
        ]);
    }
}