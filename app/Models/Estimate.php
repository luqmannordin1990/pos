<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimate extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function Items()
    {
        return $this->belongsToMany(Item::class, 'item_estimate');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
