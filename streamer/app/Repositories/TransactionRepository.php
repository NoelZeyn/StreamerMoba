<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function find(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function findOrFail(int $id): Transaction
    {
        return Transaction::findOrFail($id);
    }

    public function all()
    {
        return Transaction::all();
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);
        return $transaction;
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }
}