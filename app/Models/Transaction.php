<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }




    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'transaction_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'location');
    }
}
