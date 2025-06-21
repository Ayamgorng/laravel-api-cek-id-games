<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'game_id',
        'game_user_id',
        'game_username',
        'product_code',
        'product_name',
        'amount',
        'status',
        'provider',
        'provider_response',
        'processed_at'
    ];

    protected $casts = [
        'provider_response' => 'array',
        'amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    /**
     * Relasi dengan user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan game
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Scope berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk transaksi sukses
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope untuk transaksi pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk transaksi gagal
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
