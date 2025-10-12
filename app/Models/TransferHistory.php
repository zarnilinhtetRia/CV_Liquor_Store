<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferHistory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function from_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_location');
    }

    public function to_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_location');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function variation()
    {
        return $this->hasOne(ItemVariation::class, 'id', 'variation_id');
    }
}
