<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SubmitsFacilitiesForm;
use App\Http\Requests\StoreFacilitiesUtilizationRequest;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    use SubmitsFacilitiesForm;

    protected function requesterType(): string
    {
        return 'org';
    }

    protected function requesterUnit(): string
    {
        return Auth::user()->organization_name;
    }

    protected function submittedRedirectRoute(): string
    {
        return 'org.dashboard';
    }

    public function createFacilities()
    {
        $user = Auth::user();

        $gsuFacilities = Facility::where('owner_type', 'gsu')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $orgFacilities = Facility::where('owner_type', 'org')
            ->where('owner_org', $user->organization_name)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('org.requests.facilities_create', compact('gsuFacilities', 'orgFacilities', 'user'));
    }

    public function storeFacilities(StoreFacilitiesUtilizationRequest $request)
    {
        return $this->handleStoreFacilities($request);
    }

    public function indexFacilities()
    {
        $submissions = $this->handleIndexFacilities();
        return view('org.requests.facilities_index', compact('submissions'));
    }
}
