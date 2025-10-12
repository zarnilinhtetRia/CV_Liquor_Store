<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'transaction_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'location');
    }
}
