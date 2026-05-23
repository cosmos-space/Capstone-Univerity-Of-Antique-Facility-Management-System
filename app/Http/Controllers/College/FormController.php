<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SubmitsFacilitiesForm;
use App\Http\Requests\StoreFacilitiesUtilizationRequest;
use App\Models\Facility;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    use SubmitsFacilitiesForm;

    protected function requesterType(): string
    {
        return 'college';
    }

    protected function requesterUnit(): string
    {
        return Auth::user()->college_name;
    }

    protected function submittedRedirectRoute(): string
    {
        return 'college.dashboard';
    }

    public function createFacilities()
    {
        $user = Auth::user();

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

    public function storeFacilities(StoreFacilitiesUtilizationRequest $request)
    {
        return $this->handleStoreFacilities($request);
    }

    public function indexFacilities()
    {
        $submissions = $this->handleIndexFacilities();
        return view('college.requests.facilities_index', compact('submissions'));
    }

    public function showFacilities(FormSubmission $submission)
    {
        $user = Auth::user();

        if (!$submission->isFacilitiesUtilization() || $submission->requester_id !== $user->id) {
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
