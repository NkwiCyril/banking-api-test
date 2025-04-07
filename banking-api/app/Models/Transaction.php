<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $table = 'transactions';

    protected $fillable = [
        'sender_account_id',
        'receiver_account_id',
        'amount',
        'transaction_type',
        'status',
        'created_by',
    ];

    public function sender() {
        return $this->belongsTo(BankAccount::class, 'sender_account_id');
    }
    
    public function receiver() {
        return $this->belongsTo(BankAccount::class, 'receiver_account_id');
    }
    
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
