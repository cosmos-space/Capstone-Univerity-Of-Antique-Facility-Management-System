@extends('layouts.app')

@section('content')
<div class="flex">
    <aside class="w-64 mr-6">
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="text-sm font-semibold mb-2">GSU Admin Menu</h2>
            <ul class="space-y-1 text-sm">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600">Dashboard</a></li>
                <li><a href="#" class="text-gray-400 cursor-not-allowed" title="Coming soon">Facilities</a></li>
                <li><a href="#" class="text-gray-400 cursor-not-allowed" title="Coming soon">Bookings</a></li>
                <li><a href="#" class="text-gray-400 cursor-not-allowed" title="Coming soon">Maintenance Tickets</a></li>
                <li><a href="#" class="text-gray-400 cursor-not-allowed" title="Coming soon">Accounts</a></li>
            </ul>
        </div>
    </aside>
    <section class="flex-1">
        @yield('admin-content')
    </section>
</div>
@endsection
