<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveredItems extends Model
{
    use HasFactory,SoftDeletes;

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function sell(){
        return $this->belongsTo(Sell::class,'sell_id');
    }

    public function variation(){
        return $this->belongsTo(ItemVariation::class,'variation_id');
    }
}
