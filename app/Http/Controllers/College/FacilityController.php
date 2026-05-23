<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FacilityController extends Controller
{
    public function index()
    {
        $collegeName = Auth::user()->college_name;
        $facilities = Facility::where('owner_type', 'college')
            ->where('owner_college', $collegeName)
            ->orderBy('name')
            ->paginate(10);

        return view('college.facilities.index', compact('facilities', 'collegeName'));
    }

    public function create()
    {
        $collegeName = Auth::user()->college_name;
        return view('college.facilities.create', compact('collegeName'));
    }

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
        $validated['is_active'] = false;
        $validated['availability_status'] = 'unavailable';

        Facility::create($validated);

        return redirect()->route('college.facilities.index')
            ->with('status', 'Facility created successfully.');
    }

    public function edit(Facility $facility)
    {
        Gate::authorize('manageCollegeFacility', $facility);

        $collegeName = Auth::user()->college_name;
        return view('college.facilities.edit', compact('facility', 'collegeName'));
    }

    public function update(Request $request, Facility $facility)
    {
        Gate::authorize('manageCollegeFacility', $facility);

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

    public function destroy(Facility $facility)
    {
        Gate::authorize('manageCollegeFacility', $facility);

        $facility->delete();

        return redirect()->route('college.facilities.index')
            ->with('status', 'Facility deleted successfully.');
    }
}
