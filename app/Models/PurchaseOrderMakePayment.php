<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderMakePayment extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [''];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'payment_method');
    }
    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_record');
    }

    public function Id_po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}

