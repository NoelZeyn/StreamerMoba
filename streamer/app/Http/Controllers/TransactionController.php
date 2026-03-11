<?php

namespace App\Http\Controllers;

use App\DTO\CreateTransactionDTO;
use App\Http\Requests\CreateTransactionRequest;
use App\Services\TransactionService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Js;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class TransactionController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index()
    {
        try {
            $transactions = $this->transactionService->list();
            return $this->success($transactions, 'Transactions retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTransactionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new CreateTransactionDTO(
                $validated['user_id'],
                $validated['player_id'],
                $validated['quantity'],
                $validated['price']
            );

            $transaction = $this->transactionService->createTransaction($dto);

            return $this->success($transaction, 'Transaction created successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $transaction = $this->transactionService->find($id);
            return $this->success($transaction, 'Transaction retrieved successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        try {
            DB::transaction(function () use ($id) {
                $transaction = $this->transactionService->find($id);

                if (!$transaction) {
                    throw new Exception('Transaction not found');
                }

                $this->authorize('delete', $transaction);

                $this->transactionService->delete($id);
                return $this->success(null, 'Transaction deleted successfully');
            });
            
        } catch (Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
