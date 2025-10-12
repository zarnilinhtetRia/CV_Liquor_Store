<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [''];

    // public function items()
    // {
    //     return $this->hasMany(Item::class);
    // }

    public function inouts()
    {
        return $this->hasMany(InOut::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'level');
    }
    public function po_sells()
    {
        return $this->hasMany(PO_sells::class, 'warehouse', 'id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class, 'warehouse_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'branch');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'branch');
    }

    public function product_lists()
    {
        return $this->hasMany(ProductList::class, 'warehouse_id');
    }

    public function from_transfer_histories()
    {
        return $this->hasMany(TransferHistory::class, 'from_location', 'id');
    }
}
