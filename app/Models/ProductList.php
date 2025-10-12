<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductList extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function brand_variations()
    {
        return $this->belongsTo(BrandVariations::class,'brand_variation_id');
    }

    public function po_sells()
    {
        return $this->hasMany(PO_sells::class, 'id', 'product_list_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
}
