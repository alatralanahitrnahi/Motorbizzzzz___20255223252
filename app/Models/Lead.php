<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class Lead extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'assigned_to',
        'lead_source',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'status',
        'estimated_value',
        'probability',
        'expected_close_date',
        'notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'probability' => 'integer',
        'expected_close_date' => 'date',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function convertToCustomer($customerData = [])
    {
        $customer = Customer::create(array_merge([
            'business_id' => $this->business_id,
            'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
            'name' => $customerData['name'] ?? $this->contact_person,
            'email' => $customerData['email'] ?? $this->email,
            'phone' => $customerData['phone'] ?? $this->phone,
            'company_name' => $customerData['company_name'] ?? $this->company_name,
            'status' => 'active',
        ], $customerData));

        $this->update(['status' => 'converted']);

        return $customer;
    }
}
