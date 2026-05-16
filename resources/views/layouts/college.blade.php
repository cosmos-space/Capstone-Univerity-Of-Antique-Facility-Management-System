@extends('layouts.app')

@section('content')
<div class="flex">
    <aside class="w-64 mr-6">
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="text-sm font-semibold mb-2">College Staff Menu</h2>
            <ul class="space-y-1 text-sm">
                <li><a href="{{ route('college.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                <li><a href="{{ route('college.facilities.index') }}" class="text-blue-600 hover:text-blue-800">My Facilities</a></li>
                <li><a href="{{ route('college.bookings.index') }}" class="text-blue-600 hover:text-blue-800">Bookings</a></li>
            </ul>
        </div>
        
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">College Info</h2>
            <p class="text-xs text-gray-600">
                {{ optional(auth()->user())->college_name ?? 'Not set' }}
            </p>
        </div>
    </aside>
    <section class="flex-1">
        @yield('college-content')
    </section>
</div>
@endsection