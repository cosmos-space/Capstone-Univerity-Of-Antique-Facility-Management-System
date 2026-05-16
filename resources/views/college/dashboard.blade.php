@extends('layouts.college')

@section('college-content')
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-semibold mb-4">College Staff Dashboard</h1>
        <p class="text-gray-700 mb-4">
            Welcome to the College Staff Portal. Manage your college's facilities and booking requests from here.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-blue-50 rounded p-4 border border-blue-200">
                <h3 class="font-semibold text-blue-800 mb-2">My Facilities</h3>
                <p class="text-sm text-blue-600">View and manage facilities owned by your college</p>
            </div>
            <div class="bg-green-50 rounded p-4 border border-green-200">
                <h3 class="font-semibold text-green-800 mb-2">Booking Requests</h3>
                <p class="text-sm text-green-600">Review and manage booking requests for your facilities</p>
            </div>
        </div>
    </div>
@endsection
