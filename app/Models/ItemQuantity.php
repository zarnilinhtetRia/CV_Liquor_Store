<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemQuantity extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [''];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function variations()
    {
        return $this->belongsTo(ItemVariation::class,'variation_id');
    }
}
