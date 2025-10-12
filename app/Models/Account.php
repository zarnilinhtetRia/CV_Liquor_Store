<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class, 'account_id');
    }


    public  function  warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'location');
    }
}
