@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    $monthLabel = $currentMonth->format('F Y');
    $daysInMonth = $currentMonth->daysInMonth;
    $firstWeekday = $currentMonth->copy()->startOfMonth()->dayOfWeekIso;
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
@endphp

<div class="fms-card">
    <div class="fms-page-header border-0 pb-0 mb-4">
        <div>
            <h1 class="fms-page-title">Facility Schedule</h1>
            <p class="text-xs text-neutral-600">
                Public read-only view of confirmed facility bookings. Contact GSU for changes.
            </p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('home', ['month' => $prevMonth]) }}" class="fms-link">← Prev</a>
            <span class="text-neutral-600">{{ $monthLabel }}</span>
            <a href="{{ route('home', ['month' => $nextMonth]) }}" class="fms-link">Next →</a>
        </div>
    </div>

    <div class="mb-2 text-xs text-neutral-600">
        Only approved and rescheduled bookings are shown here.
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
                            <td class="align-top border-r border-black last:border-r-0 p-1 h-32">
                                @if ($cellCount >= $firstWeekday && $dayCounter <= $daysInMonth)
                                    @php
                                        $dateObj = $currentMonth->copy()->day($dayCounter);
                                        $dateKey = $dateObj->toDateString();
                                        $dayBookings = $days[$dateKey] ?? [];
                                    @endphp
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] font-semibold">{{ $dayCounter }}</span>
                                        @if (count($dayBookings) > 0)
                                            <span class="text-[10px] text-neutral-500">{{ count($dayBookings) }} booking{{ count($dayBookings) > 1 ? 's' : '' }}</span>
                                        @endif
                                    </div>

                                    @foreach ($dayBookings as $booking)
                                        <div class="mb-1 border border-black px-1 py-0.5 bg-white">
                                            <div class="text-[10px] font-semibold">{{ optional($booking->facility)->name ?? 'Unknown facility' }}</div>
                                            <div class="text-[10px] text-neutral-600">{{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }}</div>
                                            <div class="text-[10px] text-neutral-600">{{ \Illuminate\Support\Str::limit($booking->purpose ?? 'No purpose provided', 60) }}</div>
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

    @auth
        <p class="text-xs text-neutral-600">
            Signed in as {{ auth()->user()->name }}.
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="fms-link">Logout</a>
        </p>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    @else
        <p class="text-xs text-neutral-600">
            Staff? <a href="{{ route('login') }}" class="fms-link">Sign in</a> to access your portal.
        </p>
    @endauth
</div>
@endsection
