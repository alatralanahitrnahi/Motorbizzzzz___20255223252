<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class Customer extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'customer_code',
        'name',
        'email',
        'phone',
        'company_name',
        'gstin',
        'billing_address',
        'shipping_address',
        'customer_type',
        'credit_limit',
        'payment_terms',
        'status',
        'tags',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms' => 'integer',
        'tags' => 'array',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
