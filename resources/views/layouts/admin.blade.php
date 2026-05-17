@extends('layouts.app')

@section('content')
<div class="flex">
    <aside class="w-64 mr-6">
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="text-sm font-semibold mb-2">GSU Admin Menu</h2>
            <ul class="space-y-1 text-sm">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                <li><a href="{{ route('admin.facilities.index') }}" class="text-blue-600 hover:text-blue-800">Facilities</a></li>
                <li><a href="{{ route('admin.forms.facilities.index') }}" class="text-blue-600 hover:text-blue-800">Facilities Requests</a></li>
                <li><a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">User Management</a></li>
            </ul>
        </div>
    </aside>
    <section class="flex-1">
        @yield('admin-content')
    </section>
</div>
@endsection
