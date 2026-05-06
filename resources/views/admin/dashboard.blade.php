@extends('layouts.admin')

@section('admin-content')
    <h1 class="text-2xl font-semibold mb-4">GSU Admin Dashboard</h1>
    <p class="text-sm text-gray-700">
        This is where monthly statistics, request verification, and facility usage overview will appear.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">Total Bookings</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">Pending Requests</h3>
            <p class="text-3xl font-bold text-yellow-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">Active Facilities</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">Maintenance Tickets</h3>
            <p class="text-3xl font-bold text-red-600 mt-2">0</p>
        </div>
    </div>
@endsection
