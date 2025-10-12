<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [''];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function item_quantity()
    {
        return $this->hasOne(ItemQuantity::class, 'variation_id');
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'variation_id');
    }
}
