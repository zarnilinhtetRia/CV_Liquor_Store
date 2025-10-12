<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $guarded = [''];
    public function inOuts()
    {
        return $this->hasMany(InOut::class, 'items_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variations()
    {
        return $this->hasMany(ItemVariation::class, 'item_id');
    }

    public function item_quantity()
    {
        return $this->hasMany(ItemQuantity::class, 'item_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function brand_variation()
    {
        return $this->belongsTo(BrandVariations::class, 'brand_variation_id');
    }

    public function product_lists()
    {
        return $this->hasMany(ProductList::class, 'item_id', 'id');
    }

    public function deliveredItems()
    {
        return $this->hasMany(DeliveredItems::class, 'item_id', 'id');
    }

    public function transferHistory()
    {
        return $this->belongsTo(TransferHistory::class, 'item_id');
    }
}
