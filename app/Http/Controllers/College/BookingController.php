<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GroupsBookingsByDay;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use GroupsBookingsByDay;

    public function calendar(Request $request)
    {
        $user = Auth::user();
        $collegeName = $user->college_name;

        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $collegeFacilityIds = Facility::where('owner_type', 'college')
            ->where('owner_college', $collegeName)
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

        $days = $this->groupByDay($bookings);

        $facilityCounts = Facility::whereIn('id', $collegeFacilityIds)
            ->orderBy('name')
            ->get()
            ->map(function ($facility) use ($start, $end) {
                $count = $facility->bookings()
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
