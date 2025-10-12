<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PO_sells extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function product_list()
    {
        return $this->belongsTo(ProductList::class, 'product_list_id');
    }

    public function branch()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }
}
