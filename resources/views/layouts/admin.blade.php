@extends('layouts.app')

@section('main-class', 'mx-auto w-full max-w-[1440px] flex-1 px-0 py-0')

@section('content')
<div class="fms-shell">
    @include('layouts.partials.sidebar', [
        'portalTitle' => 'GSU Admin',
        'portalSubtitle' => optional(auth()->user())->name,
        'sections' => [
            [
                'heading' => 'Overview',
                'links' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ],
            ],
            [
                'heading' => 'Facilities',
                'links' => [
                    ['label' => 'All facilities', 'route' => 'admin.facilities.index'],
                    ['label' => 'Add facility', 'route' => 'admin.facilities.create'],
                ],
            ],
            [
                'heading' => 'Requests',
                'links' => [
                    ['label' => 'Utilization requests', 'route' => 'admin.forms.facilities.index', 'routes' => 'admin.forms.facilities.*'],
                    ['label' => 'Bookings', 'route' => 'admin.bookings.index', 'routes' => 'admin.bookings.*'],
                    ['label' => 'Booking calendar', 'route' => 'admin.calendar'],
                    ['label' => 'Monthly overview', 'route' => 'admin.overview'],
                ],
            ],

            [
                'heading' => 'Administration',
                'links' => [
                    ['label' => 'Users', 'route' => 'admin.users.index'],
                    ['label' => 'Create user', 'route' => 'admin.users.create'],
                    ['label' => 'Signatories', 'route' => 'admin.signatories.index'],
                ],
            ],
        ],
    ])

    <section class="fms-main">
        @yield('admin-content')
    </section>
</div>
@endsection
