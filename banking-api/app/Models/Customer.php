<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
    ];

    public function bankAccounts(): HasMany 
    {
        return $this->hasMany(BankAccount::class);
    }
}