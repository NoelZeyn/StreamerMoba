<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    /**
     * Menampilkan riwayat donasi untuk UI Vue
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();
        $donations = $this->donationService->getDonationHistory($userId);

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Detail donasi (jika ingin melihat raw payload)
     */
    public function show($id): JsonResponse
    {
        $donation = \App\Models\Donation::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $donation
        ]);
    }
}