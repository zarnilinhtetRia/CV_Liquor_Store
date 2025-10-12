<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InvoicePaymentMethod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [''];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'payment_method', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
