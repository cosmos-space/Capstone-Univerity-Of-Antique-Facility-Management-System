<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GroupsBookingsByDay;
use App\Models\Booking;
use Illuminate\Http\Request;

class PublicCalendarController extends Controller
{
    use GroupsBookingsByDay;

    public function index(Request $request)
    {
        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $bookings = Booking::with('facility')
            ->whereBetween('start_time', [$start, $end])
            ->whereIn('status', ['approved', 'rescheduled'])
            ->orderBy('start_time')
            ->get();

        $days = $this->groupByDay($bookings);

        return view('public.calendar', [
            'currentMonth' => $current,
            'days' => $days,
        ]);
    }
}
