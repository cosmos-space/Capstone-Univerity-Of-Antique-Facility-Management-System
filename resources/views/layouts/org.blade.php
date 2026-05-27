@extends('layouts.app')

{{-- Removed max-w-[1440px] to MAXIMIZE the UI width across the entire screen --}}
@section('main-class', 'w-full flex-1 px-0 py-0')

@section('content')
<div class="fms-shell">
    @include('layouts.partials.sidebar', [
        'portalTitle' => 'Organization Staff',
        'portalSubtitle' => optional(auth()->user())->organization_name ?? 'Organization not set',
        'sections' => [
            [
                'heading' => 'Overview',
                'links' => [
                    ['label' => 'Dashboard', 'route' => 'org.dashboard'],
                    ['label' => 'Booking calendar', 'route' => 'org.bookings.index'],
                ],
            ],
            [
                'heading' => 'Requests',
                'links' => [
                    ['label' => 'My requests', 'route' => 'org.requests.facilities.index', 'routes' => 'org.requests.facilities.*'],
                    ['label' => 'New request', 'route' => 'org.requests.facilities.create', 'routes' => 'org.requests.facilities.*'],
                ],
            ],
        ],
        'footer' => '<span class="text-black font-medium">Organization</span><br>' . e(optional(auth()->user())->organization_name ?? 'Not set'),
    ])

    <section class="fms-main">
        <div class="fms-content-wrapper">
            @yield('org-content')
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
