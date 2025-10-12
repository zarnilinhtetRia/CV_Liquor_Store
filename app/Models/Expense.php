<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
    public function catego()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'branch', 'id');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category', 'id');
    }
}
