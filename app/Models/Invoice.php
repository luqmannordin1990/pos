<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_invoice');
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'invoice_payments');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
