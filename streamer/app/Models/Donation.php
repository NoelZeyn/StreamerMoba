<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'donator_name',
        'email',
        'message',
        'amount',
        'currency',
        'saweria_id',
        'raw_payload',
        'donated_at'
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'donated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}