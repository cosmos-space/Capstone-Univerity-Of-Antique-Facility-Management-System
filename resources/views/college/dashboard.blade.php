@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">College Dashboard</h1>
    <p class="text-sm text-gray-700">
        Welcome to the College Staff Dashboard. Here you can manage facility requests and view your college's bookings.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">My Bookings</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">Pending Requests</h3>
            <p class="text-3xl font-bold text-yellow-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800">Approved Requests</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">0</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow mt-6">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="space-x-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                New Facility Request
            </button>
            <button class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                View Calendar
            </button>
        </div>
    </div>
@endsection
