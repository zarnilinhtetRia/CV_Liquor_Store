<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandVariations extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function item(){
        return $this->hasOne(Item::class,'brand_variation_id');
    }

    public function product_lists()
    {
        return $this->hasMany(ProductList::class,'brand_variation_id','id');
    }
}
