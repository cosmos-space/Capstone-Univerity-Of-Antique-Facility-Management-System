<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Read-only booking calendar for organization staff.
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();

        $month = $request->query('month');
        $current = $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $bookings = Booking::with('facility')
            ->whereBetween('start_time', [$start, $end])
            ->where('requester_id', $user->id)
            ->orderBy('start_time')
            ->get();

        $days = [];
        foreach ($bookings as $booking) {
            $dayKey = $booking->start_time->toDateString();
            if (!isset($days[$dayKey])) {
                $days[$dayKey] = [];
            }
            $days[$dayKey][] = $booking;
        }

        return view('org.bookings.calendar', [
            'currentMonth' => $current,
            'days' => $days,
        ]);
    }
}
