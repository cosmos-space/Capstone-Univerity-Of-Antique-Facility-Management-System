<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities.
     */
    public function index()
    {
        $facilities = Facility::orderBy('name')->paginate(10);
        return view('admin.facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new facility.
     */
    public function create()
    {
        return view('admin.facilities.create');
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'owner_type' => 'required|in:gsu,college,org',
            'owner_college' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'availability_status' => 'nullable|in:available,unavailable,maintenance',
        ]);

        Facility::create($validated);

        return redirect()->route('admin.facilities.index')
            ->with('status', 'Facility created successfully.');
    }

    /**
     * Show the form for editing the specified facility.
     */
    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'location'           => 'required|string|max:255',
            'owner_type'         => 'required|in:gsu,college,org',
            'owner_college'      => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'is_active'          => 'boolean',
            'availability_status'=> 'nullable|in:available,unavailable,maintenance',
        ]);

        // For core seeded facilities, do not allow changing owner_type / owner_college
        if ($facility->isCoreFacility()) {
            unset($validated['owner_type'], $validated['owner_college']);
        }

        // Admin cannot change availability_status for college-owned facilities
        if ($facility->owner_type === 'college') {
            unset($validated['availability_status']);
        }

        $facility->update($validated);

        return redirect()->route('admin.facilities.index')
            ->with('status', 'Facility updated successfully.');
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Facility $facility)
    {
        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('status', 'Facility deleted successfully.');
    }
}
