<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'owner_type',
        'owner_college',
        'description',
        'is_active',
        'availability_status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
 
    /**
     * Determine if this facility is part of the seeded core UA list.
     *
     * These are the official facilities controlled either by GSU or specific colleges.
     */
    public function isCoreFacility(): bool
    {
        $coreNames = [
            'BUSALAN HALL',
            'AVR-USA HALL',
            'E-HUB',
            'BALAY NI JUAN',
            'ICT AVR',
            'CEA AVR',
            'CBA AVR',
            'NEW AVR',
            'GRAND STAND',
            'COVERED GYM',
            'TRACK OVAL',
        ];

        return in_array($this->name, $coreNames, true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }
}
 