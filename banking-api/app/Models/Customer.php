<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
    ];

    public function accounts() {
        return $this->hasMany(BankAccount::class);
    }
}