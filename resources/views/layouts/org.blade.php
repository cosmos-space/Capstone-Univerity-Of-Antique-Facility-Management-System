@extends('layouts.app')

@section('content')
<div class="flex">
    <aside class="w-64 mr-6">
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="text-sm font-semibold mb-2">Org Staff Menu</h2>
            <ul class="space-y-1 text-sm">
                <li><a href="{{ route('org.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            </ul>
        </div>
        
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Organization Info</h2>
            <p class="text-xs text-gray-600">
                {{ optional(auth()->user())->organization_name ?? 'Not set' }}
            </p>
        </div>
    </aside>
    <section class="flex-1">
        @yield('org-content')
    </section>
</div>
@endsection