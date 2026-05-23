<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Read-only calendar of bookings for college staff.
     * Shows bookings requested by the user plus bookings for college-owned facilities.
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();
        $collegeId = $user->college_id;
        $collegeName = $user->college_name;

        $month = $request->query('month');
        $current = $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $collegeFacilityIds = Facility::where('owner_type', 'college')
            ->ownedByCollege($collegeId, $collegeName)
            ->pluck('id')
            ->all();

        $bookings = Booking::with('facility')
            ->whereBetween('start_time', [$start, $end])
            ->where(function ($query) use ($user, $collegeFacilityIds) {
                $query->where('requester_id', $user->id);

                if (!empty($collegeFacilityIds)) {
                    $query->orWhereIn('facility_id', $collegeFacilityIds);
                }
            })
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

        $facilityCounts = Facility::whereIn('id', $collegeFacilityIds)
            ->orderBy('name')
            ->get()
            ->map(function ($facility) use ($start, $end) {
                $count = Booking::where('facility_id', $facility->id)
                    ->whereBetween('start_time', [$start, $end])
                    ->whereIn('status', ['approved', 'rescheduled'])
                    ->count();

                return [
                    'facility' => $facility,
                    'count' => $count,
                ];
            });

        return view('college.bookings.calendar', [
            'currentMonth' => $current,
            'days' => $days,
            'facilityCounts' => $facilityCounts,
            'collegeName' => $collegeName,
        ]);
    }
}
