@extends('layouts.admin')

@section('admin-content')
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-semibold mb-4">Admin Dashboard</h1>
        <p class="text-gray-700 mb-4">
            Welcome to the GSU Admin Portal. Manage facilities, bookings, and maintenance requests from here.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-blue-50 rounded p-4 border border-blue-200">
                <h3 class="font-semibold text-blue-800 mb-2">Facilities</h3>
                <p class="text-sm text-blue-600">Manage campus facilities and equipment</p>
            </div>
            <div class="bg-green-50 rounded p-4 border border-green-200">
                <h3 class="font-semibold text-green-800 mb-2">Bookings</h3>
                <p class="text-sm text-green-600">Review and approve facility bookings</p>
            </div>
            <div class="bg-orange-50 rounded p-4 border border-orange-200">
                <h3 class="font-semibold text-orange-800 mb-2">Maintenance</h3>
                <p class="text-sm text-orange-600">Track maintenance tickets and requests</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">GSU Forms</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('forms.facilities.show') }}" class="bg-purple-50 rounded p-4 border border-purple-200 hover:bg-purple-100 transition">
                    <h3 class="font-semibold text-purple-800 mb-2">Facilities and Utilization Form</h3>
                    <p class="text-sm text-purple-600">Generate PDF form for facility utilization requests</p>
                </a>
                <a href="{{ route('forms.repair.show') }}" class="bg-purple-50 rounded p-4 border border-purple-200 hover:bg-purple-100 transition">
                    <h3 class="font-semibold text-purple-800 mb-2">Repair and Maintenance Form</h3>
                    <p class="text-sm text-purple-600">Generate PDF form for repair and maintenance requests</p>
                </a>
            </div>
        </div>
    </div>
@endsection
