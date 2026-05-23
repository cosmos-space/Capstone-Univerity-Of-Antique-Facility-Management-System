<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GroupsBookingsByDay;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use GroupsBookingsByDay;

    public function calendar(Request $request)
    {
        $user = Auth::user();

        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $bookings = Booking::with('facility')
            ->whereBetween('start_time', [$start, $end])
            ->where('requester_id', $user->id)
            ->orderBy('start_time')
            ->get();

        $days = $this->groupByDay($bookings);

        return view('org.bookings.calendar', [
            'currentMonth' => $current,
            'days' => $days,
        ]);
    }
}
