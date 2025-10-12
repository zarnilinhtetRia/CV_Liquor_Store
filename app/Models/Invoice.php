<?php

namespace App\Models;

use App\Models\Sell;
use App\Models\Customer;
use App\Models\PO_sells;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guraded = [];
    public function sells()
    {
        return $this->hasMany(Sell::class, 'invoiceid');
    }

    public function po_sells()
    {
        return $this->hasMany(PO_sells::class, 'invoiceid');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
    public function makePayments()
    {
        return $this->hasMany(MakePayment::class, 'invoice_id', 'id');
    }
    public function PaymentMethod()
    {
        return $this->hasMany(InvoicePaymentMethod::class, 'invoice_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'branch', 'id');
    }

    public function deliveredItems()
    {
        return $this->hasMany(DeliveredItems::class, 'invoice_id');
    }
}
