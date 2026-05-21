@extends('layouts.app')

@section('content')
    <div class="fms-card">
        <div class="fms-page-header">
            <h1 class="fms-page-title">Notifications</h1>
        </div>

        @if ($notifications->count() === 0)
            <p class="text-sm text-neutral-600">No notifications yet.</p>
        @else
            @php
                // group notifications by high-level category
                $grouped = [
                    'requests' => [],
                    'decisions' => [],
                    'bookings' => [],
                    'other' => [],
                ];

                foreach ($notifications as $n) {
                    switch ($n->type) {
                        case 'form_pending':
                        case 'form_pending_admin':
                            $grouped['requests'][] = $n;
                            break;

                        case 'form_approved':
                        case 'form_disapproved':
                            $grouped['decisions'][] = $n;
                            break;

                        case 'booking_created':
                        case 'booking_rescheduled':
                        case 'booking_cancelled':
                            $grouped['bookings'][] = $n;
                            break;

                        default:
                            $grouped['other'][] = $n;
                            break;
                    }
                }
            @endphp

            {{-- Pending / new requests --}}
            @if(count($grouped['requests']) > 0)
                <div class="mb-6">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">New / pending requests</h2>
                    <div class="fms-table-wrap">
                        <table class="fms-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>When</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grouped['requests'] as $notification)
                                    <tr>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="fms-badge">Read</span>
                                            @else
                                                <span class="fms-badge">New</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ $notification->message ?? '' }}</td>
                                        <td>{{ optional($notification->created_at)->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if (!$notification->is_read)
                                                <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="fms-link text-xs">Mark as read</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Decisions on my requests (approved / disapproved) --}}
            @if(count($grouped['decisions']) > 0)
                <div class="mb-6">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">Decisions on your requests</h2>
                    <div class="fms-table-wrap">
                        <table class="fms-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>When</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grouped['decisions'] as $notification)
                                    <tr>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="fms-badge">Read</span>
                                            @else
                                                <span class="fms-badge">New</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ $notification->message ?? '' }}</td>
                                        <td>{{ optional($notification->created_at)->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if (!$notification->is_read)
                                                <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="fms-link text-xs">Mark as read</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Booking lifecycle (created, rescheduled, cancelled) --}}
            @if(count($grouped['bookings']) > 0)
                <div class="mb-6">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">Bookings & schedule changes</h2>
                    <div class="fms-table-wrap">
                        <table class="fms-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>When</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grouped['bookings'] as $notification)
                                    <tr>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="fms-badge">Read</span>
                                            @else
                                                <span class="fms-badge">New</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ $notification->message ?? '' }}</td>
                                        <td>{{ optional($notification->created_at)->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if (!$notification->is_read)
                                                <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="fms-link text-xs">Mark as read</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Anything else --}}
            @if(count($grouped['other']) > 0)
                <div class="mb-6">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">Other</h2>
                    <div class="fms-table-wrap">
                        <table class="fms-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>When</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grouped['other'] as $notification)
                                    <tr>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="fms-badge">Read</span>
                                            @else
                                                <span class="fms-badge">New</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ $notification->message ?? '' }}</td>
                                        <td>{{ optional($notification->created_at)->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if (!$notification->is_read)
                                                <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="fms-link text-xs">Mark as read</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
