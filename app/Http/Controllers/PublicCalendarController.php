<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month');
        $current = $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $bookings = Booking::with('facility')
            ->whereBetween('start_time', [$start, $end])
            ->whereIn('status', ['approved', 'rescheduled'])
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

        return view('public.calendar', [
            'currentMonth' => $current,
            'days' => $days,
        ]);
    }
}
