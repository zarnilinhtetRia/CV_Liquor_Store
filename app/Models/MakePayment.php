<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MakePayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [''];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'payment_method');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_record');
    }

    public function Id_invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
