<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Services\BookingService;
use App\Services\NotificationService;

class FormSubmissionController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notifications
    ) {}

    public function index()
    {
        $submissions = FormSubmission::with('requester')
            ->where('type', 'facilities_utilization')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.forms.facilities_index', compact('submissions'));
    }

    public function show(FormSubmission $submission)
    {
        if (!$submission->isFacilitiesUtilization()) {
            abort(404);
        }

        $payload = $submission->payload ?? [];
        $facility = null;

        if (!empty($payload['facility_id'])) {
            $facility = \App\Models\Facility::find($payload['facility_id']);
        }

        return view('admin.forms.facilities_show', compact('submission', 'payload', 'facility'));
    }

    public function approve(FormSubmission $submission)
    {
        if (!$submission->isFacilitiesUtilization()) {
            abort(404);
        }

        $submission->markApproved();

        if ($submission->requester) {
            $this->notifications->notifyFormApproved($submission->requester_id, $submission->id);
        }

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', 'Request approved. Requester must proceed to GSU office to sign and finalize the form.');
    }

    public function disapprove(FormSubmission $submission)
    {
        if (!$submission->isFacilitiesUtilization()) {
            abort(404);
        }

        $submission->markDisapproved();

        if ($submission->requester) {
            $this->notifications->notifyFormDisapproved($submission->requester_id, $submission->id);
        }

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', 'Request disapproved.');
    }

    public function setBooking(FormSubmission $submission)
    {
        if (!$submission->isFacilitiesUtilization()) {
            abort(404);
        }

        if (!$submission->isApproved()) {
            return back()->withErrors(['status' => 'Only approved requests can be converted to bookings.']);
        }

        $result = $this->bookingService->createFromSubmission($submission);

        if (is_string($result)) {
            return back()->withErrors(['status' => $result]);
        }

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', "Booking created (Code: {$result->booking_code}) and request marked as converted.");
    }
}
