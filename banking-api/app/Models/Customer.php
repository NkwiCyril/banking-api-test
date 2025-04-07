<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function accounts() {
        return $this->hasMany(BankAccount::class);
    }
}