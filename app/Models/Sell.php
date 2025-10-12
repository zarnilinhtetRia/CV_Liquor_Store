<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sell extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [''];

    public function variations()
    {
        return $this->hasMany(ItemVariation::class, 'id', 'variation_id');
    }

    public function deliveredItems()
    {
        return $this->hasMany(DeliveredItems::class, 'sell_id');
    }

    public function invoice()
    {

        return $this->belongsTo(Invoice::class, 'invoiceid');
    }

    public function item()
    {

        return $this->belongsTo(Item::class, 'item_id');
    }

    public function branch()
    {

        return $this->belongsTo(Warehouse::class, 'warehouse');
    }
}
