@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded shadow p-6">
        <h1 class="text-xl font-semibold mb-4">Sign in</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-800 rounded px-4 py-2 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full border rounded px-3 py-2 text-sm"
                >
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full border rounded px-3 py-2 text-sm"
                >
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-blue-700"
                >
                    Login
                </button>
            </div>
        </form>
    </div>
@endsection
