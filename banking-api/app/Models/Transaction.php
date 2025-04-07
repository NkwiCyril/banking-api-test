<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    
    protected $table = 'transactions';

    protected $fillable = [
        'sender_account_id',
        'receiver_account_id',
        'amount',
        'type',
        'status',
        'description',
        'created_by',
    ];

    public function sender(): BelongsTo 
    {
        return $this->belongsTo(BankAccount::class, 'sender_account_id');
    }
    
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'receiver_account_id');
    }
    
    public function createdBy(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
