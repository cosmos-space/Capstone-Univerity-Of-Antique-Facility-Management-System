<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{ 
    /**
     * List facilities utilization form submissions.
     */
    public function index()
    {
        $submissions = FormSubmission::with('requester')
            ->where('type', 'facilities_utilization')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.forms.facilities_index', compact('submissions'));
    }

    /**
     * Show a single submission.
     */
    public function show(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        $payload = $submission->payload ?? [];
        $facility = null;

        if (!empty($payload['facility_id'])) {
            $facility = Facility::find($payload['facility_id']);
        }

        return view('admin.forms.facilities_show', compact('submission', 'payload', 'facility'));
    }

    /**
     * Approve a submission (GSU side).
     */
    public function approve(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        $submission->status = 'approved';
        $submission->save();

        // Notify requester: approved, go to GSU office
        if ($submission->requester) {
            Notification::create([
                'user_id' => $submission->requester_id,
                'type'    => 'form_approved',
                'title'   => 'Facilities utilization request approved',
                'message' => 'Please proceed to the GSU office to sign and finalize the form.',
                'data'    => ['submission_id' => $submission->id],
            ]);
        }

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', 'Request approved. Requester must proceed to GSU office to sign and finalize the form.');
    }

    /**
     * Disapprove a submission.
     */
    public function disapprove(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        $submission->status = 'disapproved';
        $submission->save();

        if ($submission->requester) {
            Notification::create([
                'user_id' => $submission->requester_id,
                'type'    => 'form_disapproved',
                'title'   => 'Facilities utilization request disapproved',
                'message' => 'Your request was disapproved by GSU.',
                'data'    => ['submission_id' => $submission->id],
            ]);
        }

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', 'Request disapproved.');
    }

    /**
     * Convert an approved facilities form into a Booking.
     *
     * This enforces:
     * - Only approved submissions are allowed.
     * - Facility must not be unavailable/maintenance.
     * - No overlapping approved bookings for the same facility & time.
     */
    public function setBooking(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        if ($submission->status !== 'approved') {
            return back()->withErrors(['status' => 'Only approved requests can be converted to bookings.']);
        }

        $payload = $submission->payload ?? null;
        if (!$payload) {
            return back()->withErrors(['status' => 'Submission payload is missing.']);
        }

        // Extract basic fields
        $facilityId   = $payload['facility_id'] ?? null;
        $dateActivity = $payload['date_activity'] ?? null;
        $timeRange    = $payload['time_range'] ?? null;

        if (!$facilityId || !$dateActivity || !$timeRange || empty($timeRange['start']) || empty($timeRange['end'])) {
            return back()->withErrors(['status' => 'Incomplete date/time or facility information in the submission.']);
        }

        $facility = Facility::find($facilityId);
        if (!$facility) {
            return back()->withErrors(['status' => 'Facility not found.']);
        }

        // Basic state check: block if facility is marked unavailable or maintenance
        if (in_array($facility->availability_status, ['unavailable', 'maintenance'], true)) {
            return back()->withErrors(['status' => "Facility is currently {$facility->availability_status} and cannot be booked."]);
        }

        // Build full datetime from date_activity + start/end times
        $startDateTime = \Carbon\Carbon::parse($dateActivity . ' ' . $timeRange['start']);
        $endDateTime   = \Carbon\Carbon::parse($dateActivity . ' ' . $timeRange['end']);

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return back()->withErrors(['status' => 'End time must be after start time.']);
        }

        // Concurrency check: no overlapping APPROVED bookings for same facility
        $overlapExists = Booking::where('facility_id', $facility->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('start_time', [$startDateTime, $endDateTime])
                  ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                  ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                      $q2->where('start_time', '<=', $startDateTime)
                         ->where('end_time', '>=', $endDateTime);
                  });
            })
            ->exists();

        if ($overlapExists) {
            return back()->withErrors(['status' => 'This facility is already booked at the requested time.']);
        }

        // Create booking
        $requester = $submission->requester;
        if (!$requester) {
            return back()->withErrors(['status' => 'Requester account not found.']);
        }

        $booking = Booking::create([
            'requester_id'   => $requester->id,
            'facility_id'    => $facility->id,
            'start_time'     => $startDateTime,
            'end_time'       => $endDateTime,
            'requester_type' => $submission->requester_type,
            'requester_unit' => $submission->requester_unit,
            'status'         => 'approved', // Because this is created from an already-approved form
            'request_method' => 'online_form',
            'purpose'        => $payload['purpose'] ?? null,
            'additional_details' => json_encode([
                'form_submission_id' => $submission->id,
                'equipment'          => $payload['equipment'] ?? [],
            ]),
            'requested_at'   => $payload['date_request'] ?? now(),
            'approved_at'    => now(),
            'booking_code'   => 'BKG-' . now()->format('YmdHis') . '-' . $facility->id,
        ]);

        // Mark submission as converted
        $submission->status = 'converted';
        $submission->save();

        // Notify requester: booking created
        if ($submission->requester) {
            Notification::create([
                'user_id' => $submission->requester_id,
                'type'    => 'booking_created',
                'title'   => 'Booking confirmed',
                'message' => "Your facilities request has been converted into a booking ({$booking->booking_code}).",
                'data'    => [
                    'submission_id' => $submission->id,
                    'booking_id'    => $booking->id,
                ],
            ]);
        }

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', "Booking created (Code: {$booking->booking_code}) and request marked as converted.");
    }

}
