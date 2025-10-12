<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PurchaseOrderPaymentMethod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [''];
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'payment_method', 'id');
    }
}
