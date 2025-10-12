<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class Invoice extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'invoice_number',
        'customer_name',
        'customer_email',
        'amount',
        'status',
        'due_date',
        'issued_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'issued_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}