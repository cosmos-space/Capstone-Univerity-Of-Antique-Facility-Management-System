@extends('layouts.admin')

@section('admin-content')
@php
    $monthLabel = $currentMonth->format('F Y');
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
    $labels = array_keys($series);
    $data = array_values($series);
@endphp

<div class="fms-card">
    <div class="fms-page-header border-0 pb-0 mb-4">
        <div>
            <h1 class="fms-page-title">Monthly Overview</h1>
            <p class="text-xs text-neutral-600">Bookings per day for approved and rescheduled facility bookings this month.</p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.overview', ['month' => $prevMonth]) }}" class="fms-link">← Prev</a>
            <span class="text-neutral-600">{{ $monthLabel }}</span>
            <a href="{{ route('admin.overview', ['month' => $nextMonth]) }}" class="fms-link">Next →</a>
        </div>
    </div>

    <div class="border border-black p-4 overflow-x-auto">
        <canvas id="fmsOverviewChart" width="900" height="320"></canvas>
    </div>
</div>

<script>
    (function() {
        const labels = @json($labels);
        const data = @json($data);

        const canvas = document.getElementById('fmsOverviewChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;

        const paddingLeft = 40;
        const paddingRight = 20;
        const paddingTop = 20;
        const paddingBottom = 40;

        const maxVal = Math.max(1, Math.max.apply(null, data));
        const chartWidth = width - paddingLeft - paddingRight;
        const chartHeight = height - paddingTop - paddingBottom;

        ctx.clearRect(0, 0, width, height);
        ctx.font = '10px sans-serif';
        ctx.fillStyle = '#000';
        ctx.strokeStyle = '#000';

        ctx.beginPath();
        ctx.moveTo(paddingLeft, paddingTop);
        ctx.lineTo(paddingLeft, height - paddingBottom);
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(paddingLeft, height - paddingBottom);
        ctx.lineTo(width - paddingRight, height - paddingBottom);
        ctx.stroke();

        ctx.fillText('0', 8, height - paddingBottom + 12);
        ctx.fillText(String(maxVal), 8, paddingTop + 4);

        const n = labels.length;
        const stepX = chartWidth / Math.max(1, n - 1);

        labels.forEach((label, i) => {
            const x = paddingLeft + i * stepX;
            if (n <= 31 && (i === 0 || i === n - 1 || i % 5 === 0)) {
                ctx.fillText(String(label), x - 6, height - paddingBottom + 16);
            }
        });

        ctx.beginPath();
        ctx.strokeStyle = '#2563eb';
        ctx.lineWidth = 2;

        data.forEach((val, i) => {
            const x = paddingLeft + i * stepX;
            const y = height - paddingBottom - (val / maxVal) * chartHeight;

            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });

        ctx.stroke();

        data.forEach((val, i) => {
            const x = paddingLeft + i * stepX;
            const y = height - paddingBottom - (val / maxVal) * chartHeight;
            ctx.beginPath();
            ctx.arc(x, y, 3, 0, Math.PI * 2);
            ctx.fillStyle = '#2563eb';
            ctx.fill();
        });
    })();
</script>
