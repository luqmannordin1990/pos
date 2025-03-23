<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
