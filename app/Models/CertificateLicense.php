<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'certificate_number',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'status',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'responsible_person_id',
        'notes',
        'reminder_sent'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'file_size' => 'integer',
        'reminder_sent' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    public function isExpiringSoon()
    {
        if ($this->expiry_date) {
            $expiryDate = \Carbon\Carbon::parse($this->expiry_date);
            $now = \Carbon\Carbon::now();
            return $expiryDate->diffInDays($now) <= 30 && $expiryDate->isFuture();
        }
        return false;
    }

    public function isExpired()
    {
        if ($this->expiry_date) {
            return \Carbon\Carbon::parse($this->expiry_date)->isPast();
        }
        return false;
    }

    public function needsRenewal()
    {
        return $this->isExpiringSoon() || $this->isExpired() || $this->status === 'pending_renewal';
    }
}