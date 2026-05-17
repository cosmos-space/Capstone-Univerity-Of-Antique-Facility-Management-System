<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FormSubmission;
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

        // GSU-managed facilities + this college's own facilities
        $gsuFacilities = Facility::where('owner_type', 'gsu')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $collegeFacilities = Facility::where('owner_type', 'college')
            ->where('owner_college', $user->college_name)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('college.requests.facilities_create', compact('gsuFacilities', 'collegeFacilities', 'user'));
    }

    /**
     * Store Facilities Utilization request as a JSON form submission.
     */
    public function storeFacilities(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'date_activity' => 'required|date',
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
        ]);

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
        ];

        FormSubmission::create([
            'type'           => 'facilities_utilization',
            'requester_id'   => $user->id,
            'requester_type' => 'college',
            'requester_unit' => $user->college_name,
            'status'         => 'pending',
            'payload'        => $payload,
        ]);

        return redirect()->route('college.dashboard')
            ->with('status', 'Facilities utilization request submitted to GSU.');
    }
}
