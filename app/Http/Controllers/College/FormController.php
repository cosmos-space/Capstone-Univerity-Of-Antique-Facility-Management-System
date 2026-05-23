<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FormSubmission;
use App\Models\Notification;
use App\Models\Signatory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    /**
     * Show the Facilities Utilization online request form.
     */
    public function createFacilities()
    { 
        $user = Auth::user();
        $collegeId = $user->college_id;
        $collegeName = $user->college_name;

        // GSU-managed facilities + this college's own facilities
        $gsuFacilities = Facility::where('owner_type', 'gsu')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $collegeFacilities = Facility::where('owner_type', 'college')
            ->ownedByCollege($collegeId, $collegeName)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $deans = Signatory::where('type', 'dean')
            ->forCollege($collegeId, $collegeName)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $programHeads = Signatory::where('type', 'program_head')
            ->forCollege($collegeId, $collegeName)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('college.requests.facilities_create', compact(
            'gsuFacilities',
            'collegeFacilities',
            'user',
            'deans',
            'programHeads'
        ));
    }

    /**
     * Store Facilities Utilization request as a JSON form submission.
     */
    public function storeFacilities(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'date_activity' => 'required|date|after_or_equal:' . now()->addDays(7)->toDateString(),
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'facility_id'   => 'required|exists:facilities,id',
            'purpose'       => 'required|string|max:500',

            // Equipment (optional but non-negative)
            'qty_monobloc'  => 'nullable|integer|min:0',
            'qty_table'     => 'nullable|integer|min:0',
            'qty_fan'       => 'nullable|integer|min:0',
            'qty_rostrum'   => 'nullable|integer|min:0',
            'qty_flag'      => 'nullable|integer|min:0',
            'qty_sound'     => 'nullable|integer|min:0',
            'qty_led'       => 'nullable|integer|min:0',

            'venue_others'  => 'nullable|string|max:255',

            'noted_signatory_id'     => 'required|string',
            'noted_signatory_custom' => 'nullable|string|max:255',
        ]);

        $notedName = null;
        $raw = $request->input('noted_signatory_id');

        if ($raw === 'custom') {
            $request->validate([
                'noted_signatory_custom' => 'required|string|max:255',
            ]);
            $notedName = $request->input('noted_signatory_custom');
            $notedType = 'custom';
        } else {
            [$notedType, $idStr] = explode(':', $raw . ':');
            $signatoryId = (int) $idStr;

            if (! in_array($notedType, ['dean', 'program_head'], true) || $signatoryId <= 0) {
                return back()->withInput()->withErrors(['noted_signatory_id' => 'Please select a valid signatory.']);
            }

            $signatory = Signatory::where('id', $signatoryId)
                ->where('type', $notedType)
                ->where(function ($query) use ($user) {
                    if ($user->college_id) {
                        $query->where('college_id', $user->college_id);
                    }

                    $query->orWhere('unit', $user->college_name);
                })
                ->where('is_active', true)
                ->first();

            if (! $signatory) {
                return back()->withInput()->withErrors(['noted_signatory_id' => 'Please select a valid signatory.']);
            }

            $notedName = $signatory->name;
        }

        // Build payload JSON (no contact number, no signature)
        $payload = [
            'control_no'      => null, // GSU can set later
            'date_request'    => now()->toDateString(),
            'requester_name'  => $user->name,
            'requester_unit'  => $user->college_name,
            'date_activity'   => $validated['date_activity'],
            'time_range'      => [
                'start' => $validated['start_time'],
                'end'   => $validated['end_time'],
            ],
            'facility_id'     => (int) $validated['facility_id'],
            'venue_others'    => $request->input('venue_others', null),
            'purpose'         => $validated['purpose'],
            'equipment'       => [
                'monobloc_chair' => (int) $request->input('qty_monobloc', 0),
                'table'          => (int) $request->input('qty_table', 0),
                'electric_fan'   => (int) $request->input('qty_fan', 0),
                'rostrum'        => (int) $request->input('qty_rostrum', 0),
                'flag'           => (int) $request->input('qty_flag', 0),
                'sound'          => (int) $request->input('qty_sound', 0),
                'led'            => (int) $request->input('qty_led', 0),
            ],
            'noted_signatory_type' => $notedType,
            'noted_signatory_name' => $notedName,
        ];

        FormSubmission::create([
            'type'           => 'facilities_utilization',
            'requester_id'   => $user->id,
            'requester_type' => 'college',
            'requester_unit' => $user->college_name,
            'status'         => 'pending',
            'payload'        => $payload,
        ]);

        // Notify requester
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'form_pending',
            'title'   => 'Facilities utilization request submitted',
            'message' => 'Your request has been sent to GSU for review.',
            'data'    => ['submission_id' => FormSubmission::where('requester_id', $user->id)->latest()->first()->id ?? null],
        ]);

        // Notify admin(s)
        foreach (User::where('role', 'admin')->get() as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'form_pending_admin',
                'title'   => 'New facilities request from college',
                'message' => $user->college_name . ' submitted a new utilization request.',
                'data'    => ['submission_id' => FormSubmission::where('requester_id', $user->id)->latest()->first()->id ?? null],
            ]);
        }

        return redirect()->route('college.dashboard')
            ->with('status', 'Facilities utilization request submitted to GSU.');
    }

    /**
     * List the current college staff member's requests.
     */
    public function indexFacilities()
    {
        $user = Auth::user();

        $submissions = FormSubmission::with('requester')
            ->where('type', 'facilities_utilization')
            ->where('requester_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('college.requests.facilities_index', compact('submissions'));
    }

    /**
     * Show a single facilities utilization request for this college staff user.
     */
    public function showFacilities(FormSubmission $submission)
    {
        $user = Auth::user();

        if (
            $submission->type !== 'facilities_utilization' ||
            $submission->requester_id !== $user->id
        ) {
            abort(404);
        }

        $payload = $submission->payload ?? [];
        $facility = null;

        if (!empty($payload['facility_id'])) {
            $facility = Facility::find($payload['facility_id']);
        }

        return view('college.requests.facilities_show', compact('submission', 'payload', 'facility'));
    }
}
