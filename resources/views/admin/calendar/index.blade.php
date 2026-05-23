@extends('layouts.admin')

@section('admin-content')
@php
    use Carbon\Carbon;
    $monthLabel = $currentMonth->format('F Y');
    $daysInMonth = $currentMonth->daysInMonth;
    $firstWeekday = $currentMonth->copy()->startOfMonth()->dayOfWeekIso; // 1=Mon..7=Sun
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
@endphp

<div class="fms-card">
    <div class="fms-page-header border-0 pb-0 mb-4">
        <div>
            <h1 class="fms-page-title">Booking Calendar</h1>
            <p class="text-xs text-neutral-600">Month view of all approved bookings with facility and purpose details.</p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.calendar', ['month' => $prevMonth]) }}" class="fms-link">← Prev</a>
            <span class="text-neutral-600">{{ $monthLabel }}</span>
            <a href="{{ route('admin.calendar', ['month' => $nextMonth]) }}" class="fms-link">Next →</a>
        </div>
    </div>

    <div class="mb-8 border border-black overflow-x-auto">
        <table class="min-w-full text-xs">
            <thead class="bg-neutral-100 border-b border-black">
                <tr>
                    @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                        <th class="px-2 py-2 border-r border-black last:border-r-0 text-center font-semibold">{{ $dow }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $dayCounter = 1;
                    $cellCount = 0;
                @endphp

                @while ($dayCounter <= $daysInMonth)
                    <tr class="border-t border-black">
                        @for ($col = 1; $col <= 7; $col++)
                            @php $cellCount++; @endphp
                            @php
                                $displayDay = $cellCount >= $firstWeekday && $dayCounter <= $daysInMonth;
                                $dayBookings = [];
                                $hasBookings = false;

                                if ($displayDay) {
                                    $dateObj = $currentMonth->copy()->day($dayCounter);
                                    $dateKey = $dateObj->toDateString();
                                    $dayBookings = $days[$dateKey] ?? [];
                                    $hasBookings = count($dayBookings) > 0;
                                }
                            @endphp
                            <td class="align-top border-r border-black last:border-r-0 p-1 h-32 {{ $hasBookings ? 'bg-neutral-100' : '' }}">
                                @if ($displayDay)
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] font-semibold">{{ $dayCounter }}</span>
                                        @if ($hasBookings)
                                            <span class="text-[10px] text-neutral-500">{{ count($dayBookings) }} booking{{ count($dayBookings) > 1 ? 's' : '' }}</span>
                                        @endif
                                    </div>

                                    @foreach ($dayBookings as $booking)
                                        <div class="mb-1 border border-black px-1 py-0.5 bg-white text-[10px]">
                                            <div class="font-semibold truncate">{{ optional($booking->facility)->name ?? 'Unknown facility' }}</div>
                                            <div class="text-neutral-600">{{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }}</div>
                                            <div class="text-neutral-600 truncate">{{ $booking->requester_unit ?? ucfirst($booking->requester_type) }}</div>
                                        </div>
                                    @endforeach

                                    @php $dayCounter++; @endphp
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endwhile
            </tbody>
        </table>
    </div>

    <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">Facility booking overview ({{ $monthLabel }})</h2>

    <div class="fms-table-wrap">
        <table class="fms-table">
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Bookings this month</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($facilityCounts as $item)
                    @php
                        $facility = $item['facility'];
                        $count = $item['count'];
                        $status = $facility->availability_status ?? 'available';
                    @endphp
                    <tr>
                        <td>{{ $facility->name }}</td>
                        <td>
                            @if ($facility->owner_type === 'gsu')
                                GSU
                            @elseif ($facility->owner_type === 'college')
                                College ({{ $facility->owner_college ?? 'N/A' }})
                            @elseif ($facility->owner_type === 'org')
                                Organization
                            @else
                                {{ ucfirst($facility->owner_type ?? 'Unknown') }}
                            @endif
                        </td>
                        <td>
                            @if ($status === 'unavailable')
                                <span class="fms-badge">Unavailable</span>
                            @elseif ($status === 'maintenance')
                                <span class="fms-badge">Maintenance</span>
                            @else
                                <span class="fms-badge">Available</span>
                            @endif
                        </td>
                        <td>
                            @if ($status !== 'available')
                                <span class="text-xs text-neutral-500">N/A ({{ ucfirst($status) }})</span>
                            @else
                                {{ $count }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
