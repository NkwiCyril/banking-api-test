<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'account_number',
        'customer_id',
        'balance',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sentTransactions() {
        return $this->hasMany(Transaction::class, 'sender_account_id');
    }
    
    public function receivedTransactions() {
        return $this->hasMany(Transaction::class, 'receiver_account_id');
    }
}
