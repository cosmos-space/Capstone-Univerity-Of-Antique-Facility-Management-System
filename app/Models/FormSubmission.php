<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',           // e.g. 'facilities_utilization', 'repair_maintenance'
        'requester_id',
        'requester_type', // 'college', 'org', 'admin'
        'requester_unit', // e.g. college_name or organization_name
        'status',         // 'pending', 'approved', 'disapproved', 'cancelled', 'converted'
        'payload',        // JSON string
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
