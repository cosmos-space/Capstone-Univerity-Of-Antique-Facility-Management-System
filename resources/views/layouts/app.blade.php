<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'UA Facility Management System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
<div class="min-h-screen flex flex-col">
    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-xl font-semibold text-gray-800">
                UA Facility Management
            </a>
            <nav class="space-x-4">
                @auth
                    <span class="text-sm text-gray-600">
                        Role: {{ auth()->user()->role ?? 'user' }}
                    </span>
                    <a href="{{ route('dashboard') }}" class="text-sm text-blue-600">Dashboard</a>
                    <form class="inline" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="text-sm text-blue-600">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 max-w-6xl mx-auto w-full px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t">
        <div class="max-w-6xl mx-auto px-4 py-4 text-xs text-gray-500">
            University of Antique • GSU Facility & Equipment Management System
        </div>
    </footer>
</div>
</body>
</html>
