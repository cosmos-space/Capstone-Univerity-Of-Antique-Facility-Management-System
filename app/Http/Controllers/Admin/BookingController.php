<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
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

    /**
     * Admin reschedules / modifies an existing booking.
     * Can change facility, date/time, and equipment payload.
     * Marks status as 'rescheduled' and notifies requester.
     */
    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'facility_id'   => 'required|exists:facilities,id',
            'date_activity' => 'required|date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'purpose'       => 'required|string|max:500',
            'reason'        => 'required|string|max:500',

            // Equipment (optional)
            'qty_monobloc'  => 'nullable|integer|min:0',
            'qty_table'     => 'nullable|integer|min:0',
            'qty_fan'       => 'nullable|integer|min:0',
            'qty_rostrum'   => 'nullable|integer|min:0',
            'qty_flag'      => 'nullable|integer|min:0',
            'qty_sound'     => 'nullable|integer|min:0',
            'qty_led'       => 'nullable|integer|min:0',
        ]);

        $startDateTime = Carbon::parse($data['date_activity'].' '.$data['start_time']);
        $endDateTime   = Carbon::parse($data['date_activity'].' '.$data['end_time']);

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return back()->withErrors(['End time must be after start time.']);
        }

        // Admin can override conflicts, but we still prevent obviously insane overlaps
        // with itself; we skip other bookings on purpose because GSU has priority.

        // Merge / update additional_details
        $existingDetails = [];
        if ($booking->additional_details) {
            $existingDetails = json_decode($booking->additional_details, true) ?: [];
        }

        $equipment = [
            'monobloc_chair' => (int) ($data['qty_monobloc'] ?? ($existingDetails['equipment']['monobloc_chair'] ?? 0)),
            'table'          => (int) ($data['qty_table'] ?? ($existingDetails['equipment']['table'] ?? 0)),
            'electric_fan'   => (int) ($data['qty_fan'] ?? ($existingDetails['equipment']['electric_fan'] ?? 0)),
            'rostrum'        => (int) ($data['qty_rostrum'] ?? ($existingDetails['equipment']['rostrum'] ?? 0)),
            'flag'           => (int) ($data['qty_flag'] ?? ($existingDetails['equipment']['flag'] ?? 0)),
            'sound'          => (int) ($data['qty_sound'] ?? ($existingDetails['equipment']['sound'] ?? 0)),
            'led'            => (int) ($data['qty_led'] ?? ($existingDetails['equipment']['led'] ?? 0)),
        ];

        $updatedDetails = array_merge($existingDetails, [
            'equipment'       => $equipment,
            'reschedule_note' => $data['reason'],
            'rescheduled_at'  => now()->toDateTimeString(),
            'rescheduled_by'  => auth()->user()->name ?? 'GSU Admin',
        ]);

        // Persist booking changes
        $booking->facility_id = $data['facility_id'];
        $booking->start_time  = $startDateTime;
        $booking->end_time    = $endDateTime;
        $booking->purpose     = $data['purpose'];
        $booking->status      = 'rescheduled';
        $booking->additional_details = json_encode($updatedDetails);
        $booking->save();

        // Notify requester (college/org staff)
        if ($booking->requester) {
            Notification::create([
                'user_id' => $booking->requester_id,
                'type'    => 'booking_rescheduled',
                'title'   => 'Booking updated by GSU',
                'message' => 'GSU has changed your booking ('.$booking->booking_code.'). Reason: '.$data['reason'],
                'data'    => [
                    'booking_id'  => $booking->id,
                    'facility_id' => $booking->facility_id,
                ],
            ]);
        }

        // Notify other admin accounts (for audit; optional)
        foreach (\App\Models\User::where('role', 'admin')->where('id', '!=', auth()->id())->get() as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'booking_rescheduled_admin',
                'title'   => 'Booking updated',
                'message' => 'Booking '.$booking->booking_code.' was rescheduled by '.auth()->user()->name.'.',
                'data'    => [
                    'booking_id' => $booking->id,
                ],
            ]);
        }

        return redirect()->route('admin.bookings.index')
            ->with('status', 'Booking updated and requester notified.');
    }

    /**
     * Admin cancels an already approved/converted booking.
     * Marks booking as 'cancelled' and notifies requester.
     */
    public function cancel(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $booking->status = 'cancelled';

        $details = [];
        if ($booking->additional_details) {
            $details = json_decode($booking->additional_details, true) ?: [];
        }
        $details['cancel_reason']  = $data['reason'];
        $details['cancelled_by']   = auth()->user()->name ?? 'GSU Admin';
        $details['cancelled_at']   = now()->toDateTimeString();

        $booking->additional_details = json_encode($details);
        $booking->save();

        if ($booking->requester) {
            Notification::create([
                'user_id' => $booking->requester_id,
                'type'    => 'booking_cancelled',
                'title'   => 'Booking cancelled by GSU',
                'message' => 'Your booking ('.$booking->booking_code.') was cancelled by GSU. Reason: '.$data['reason'],
                'data'    => [
                    'booking_id' => $booking->id,
                ],
            ]);
        }

        return redirect()->route('admin.bookings.index')
            ->with('status', 'Booking cancelled and requester notified.');
    }
}
