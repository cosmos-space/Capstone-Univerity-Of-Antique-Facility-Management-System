@extends('layouts.app')

{{-- Removed max-w-[1440px] to MAXIMIZE the UI width across the entire screen --}}
@section('main-class', 'w-full flex-1 px-0 py-0')

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
                    [
                        'label'  => 'Utilization requests',
                        'route'  => 'admin.forms.facilities.index',
                        'routes' => 'admin.forms.facilities.*',
                    ],
                    [
                        'label'  => 'Bookings',
                        'route'  => 'admin.bookings.index',
                        'routes' => ['admin.bookings.index'],
                    ],
                    [
                        'label'  => 'Create Direct Booking',
                        'route'  => 'admin.bookings.create-direct',
                        'routes' => ['admin.bookings.create-direct'],
                    ],
                    [
                        'label' => 'Booking calendar',
                        'route' => 'admin.calendar',
                    ],
                    [
                        'label' => 'Monthly overview',
                        'route' => 'admin.overview',
                    ],
                ],
            ],
            [
                'heading' => 'GSU forms (PDF)',
                'links' => [
                    ['label' => 'Facilities utilization form', 'route' => 'forms.facilities.show', 'routes' => 'forms.facilities.*'],
                    ['label' => 'Repair & maintenance form', 'route' => 'forms.repair.show', 'routes' => 'forms.repair.*'],
                ],
            ],
            [
                'heading' => 'Administration',
                'links' => [
                    ['label' => 'Users', 'route' => 'admin.users.index'],
                    ['label' => 'Create user', 'route' => 'admin.users.create'],
                ],
            ],
        ],
    ])

    <section class="fms-main">
        <div class="fms-content-wrapper">
            @yield('admin-content')
        </div>

        <footer class="admin-custom-footer">
            <p>&copy; {{ date('Y') }} University of Antique. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Support</a>
                <a href="#">System Status</a>
                <a href="#">Documentation</a>
            </div>
        </footer>
    </section>
</div>

{{-- Dynamic Brand Transformer Script ── Injects the circular logo --}}
<script>
  document.addEventListener("DOMContentLoaded", function() {
      const oldBrandText = "UA Facility Management";
      const txtNodes = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
      let node;

      while (node = txtNodes.nextNode()) {
          if (node.nodeValue.trim() === oldBrandText) {
              const parent = node.parentNode;
              if (parent) {
                  const container = document.createElement('div');
                  container.className = 'header-brand-container';

                  container.innerHTML = `
                      <img src="{{ asset('image/facilities/UA-logo.png') }}" class="ua-header-logo" alt="University of Antique Logo" onerror="this.src='https://placehold.co/44x44/111/fff?text=UA'">
                      <div class="header-text-stack">
                          <div class="brand-title">University of Antique</div>
                          <div class="brand-subtitle">Facility Management System</div>
                      </div>
                  `;
                  parent.replaceChild(container, node);
                  break;
              }
          }
      }
  });
</script>
@endsection
