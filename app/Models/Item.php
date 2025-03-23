<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];


    public function Estimates()
    {
        return $this->belongsToMany(Estimate::class, 'item_estimate');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'item_invoice');
    }

    public function recurringInvoices()
    {
        return $this->belongsToMany(RecurringInvoice::class, 'item_recurringinvoices');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
