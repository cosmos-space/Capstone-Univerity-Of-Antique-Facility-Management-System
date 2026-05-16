<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'facility_id',
        'start_time',
        'end_time',
        'requester_type',
        'requester_unit',
        'status',
        'request_method',
        'purpose',
        'additional_details',
        'requested_at',
        'approved_at',
        'booking_code',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function maintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }
}
 