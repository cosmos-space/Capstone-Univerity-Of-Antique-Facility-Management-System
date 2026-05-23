<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GroupsBookingsByDay;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Facility;
use App\Services\BookingService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use GroupsBookingsByDay;

    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notifications
    ) {}

    public function index()
    {
        $bookings = Booking::with(['requester', 'facility'])
            ->orderByDesc('start_time')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function edit(Booking $booking)
    {
        $facilities = Facility::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.bookings.edit', compact('booking', 'facilities'));
    }

    public function calendar(Request $request)
    {
        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end   = $current->copy()->endOfMonth();

        $bookings = Booking::with('facility')
            ->whereBetween('start_time', [$start, $end])
            ->orderBy('start_time')
            ->get();

        $days = $this->groupByDay($bookings);

        $facilityCounts = Facility::orderBy('name')
            ->get()
            ->map(function ($facility) use ($start, $end) {
                $count = $facility->bookings()
                    ->whereBetween('start_time', [$start, $end])
                    ->whereIn('status', ['approved', 'rescheduled'])
                    ->count();

                return [
                    'facility' => $facility,
                    'count'    => $count,
                ];
            });

        return view('admin.calendar.index', [
            'currentMonth'   => $current,
            'days'           => $days,
            'facilityCounts' => $facilityCounts,
        ]);
    }

    public function overview(Request $request)
    {
        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end   = $current->copy()->endOfMonth();

        $bookings = Booking::whereBetween('start_time', [$start, $end])
            ->whereIn('status', ['approved', 'rescheduled'])
            ->get();

        $days = [];
        for ($d = 1; $d <= $current->daysInMonth; $d++) {
            $days[$d] = 0;
        }

        foreach ($bookings as $booking) {
            $day = (int) $booking->start_time->format('j');
            if (isset($days[$day])) {
                $days[$day]++;
            }
        }

        return view('admin.overview.index', [
            'currentMonth' => $current,
            'series'       => $days,
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $data = $request->validated();

        $startDateTime = Carbon::parse($data['date_activity'] . ' ' . $data['start_time']);
        $endDateTime   = Carbon::parse($data['date_activity'] . ' ' . $data['end_time']);

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return back()->withErrors(['End time must be after start time.']);
        }

        $existingEquipment = $booking->additional_details['equipment'] ?? [];
        $equipment = $this->bookingService->buildEquipmentArray($data, $existingEquipment);

        $booking->facility_id = $data['facility_id'];
        $booking->start_time  = $startDateTime;
        $booking->end_time    = $endDateTime;
        $booking->purpose     = $data['purpose'];
        $booking->markRescheduled($equipment, $data['reason'], auth()->user()->name ?? 'GSU Admin');
        $booking->save();

        if ($booking->requester) {
            $this->notifications->notifyBookingRescheduled(
                $booking->requester_id,
                $booking->booking_code,
                $data['reason'],
                $booking->id,
                $booking->facility_id
            );
        }

        $this->notifications->notifyAdminsExcept(
            auth()->id(),
            'booking_rescheduled_admin',
            'Booking updated',
            'Booking ' . $booking->booking_code . ' was rescheduled by ' . auth()->user()->name . '.',
            ['booking_id' => $booking->id]
        );

        return redirect()->route('admin.bookings.index')
            ->with('status', 'Booking updated and requester notified.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $booking->markCancelled($data['reason'], auth()->user()->name ?? 'GSU Admin');
        $booking->save();

        if ($booking->requester) {
            $this->notifications->notifyBookingCancelled(
                $booking->requester_id,
                $booking->booking_code,
                $data['reason'],
                $booking->id
            );
        }

        return redirect()->route('admin.bookings.index')
            ->with('status', 'Booking cancelled and requester notified.');
    }
}
