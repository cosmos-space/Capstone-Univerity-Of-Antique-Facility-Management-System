<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities owned by the college.
     */
    public function index()
    {
        $collegeName = Auth::user()->college_name;
        $facilities = Facility::where('owner_type', 'college')
            ->where('owner_college', $collegeName)
            ->orderBy('name')
            ->paginate(10);

        return view('college.facilities.index', compact('facilities', 'collegeName'));
    }

    /**
     * Show the form for creating a new facility.
     */
    public function create()
    {
        $collegeName = Auth::user()->college_name;
        return view('college.facilities.create', compact('collegeName'));
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(Request $request)
    {
        $collegeName = Auth::user()->college_name;

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'location'            => 'required|string|max:255',
            'description'         => 'nullable|string',
            'is_active'           => 'boolean',
            'availability_status' => 'nullable|in:available,unavailable,maintenance',
        ]);

        $validated['owner_type'] = 'college';
        $validated['owner_college'] = $collegeName;
        $validated['availability_status'] = $validated['availability_status'] ?? 'available';

        Facility::create($validated);

        return redirect()->route('college.facilities.index')
            ->with('status', 'Facility created successfully.');
    }

    /**
     * Show the form for editing the specified facility.
     */
    public function edit(Facility $facility)
    {
        // Ensure the facility belongs to the user's college
        if ($facility->owner_type !== 'college' || $facility->owner_college !== Auth::user()->college_name) {
            abort(403, 'Unauthorized access.');
        }

        $collegeName = Auth::user()->college_name;
        return view('college.facilities.edit', compact('facility', 'collegeName'));
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        // Ensure the facility belongs to the user's college
        if ($facility->owner_type !== 'college' || $facility->owner_college !== Auth::user()->college_name) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'location'            => 'required|string|max:255',
            'description'         => 'nullable|string',
            'is_active'           => 'boolean',
            'availability_status' => 'nullable|in:available,unavailable,maintenance',
        ]);

        $facility->update($validated);

        return redirect()->route('college.facilities.index')
            ->with('status', 'Facility updated successfully.');
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Facility $facility)
    {
        // Ensure the facility belongs to the user's college
        if ($facility->owner_type !== 'college' || $facility->owner_college !== Auth::user()->college_name) {
            abort(403, 'Unauthorized access.');
        }

        $facility->delete();

        return redirect()->route('college.facilities.index')
            ->with('status', 'Facility deleted successfully.');
    }
}
