<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function brands()
    {
        return $this->hasMany(Brand::class, 'category_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'branch');
    }
}
