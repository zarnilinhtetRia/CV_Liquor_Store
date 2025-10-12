<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [''];

    public function po_sells()
    {
        return $this->hasMany(PO_sells::class, 'invoiceid');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function purchasePayment()
    {
        return $this->hasMany(PurchaseOrderPaymentMethod::class, 'po_id', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'branch', 'id');
    }
}
