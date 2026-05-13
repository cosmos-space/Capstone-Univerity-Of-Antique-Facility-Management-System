<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'UA Facility Management System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        @php($role = auth()->user()->role)
                        @if ($role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Admin Dashboard
                            </a>
                        @elseif ($role === 'college_staff')
                            <a href="{{ route('college.dashboard') }}"
                               class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                College Dashboard
                            </a>
                        @elseif ($role === 'org_staff')
                            <a href="{{ route('org.dashboard') }}"
                               class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Org Dashboard
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="flex-1 rounded-br-lg rounded-bl-lg lg:rounded-bl-lg lg:rounded-br-none bg-white dark:bg-[#161615] dark:text-[#EDEDEC] p-6 pb-12 lg:p-20 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                    <h1 class="mb-1 font-medium">University of Antique</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">GSU Facility & Equipment Management System</p>
                    <ul class="flex flex-col lg:mb-2 mb-6 space-y-2">
                        <li>
                            <span class="flex items-center gap-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                <span class="flex items-center justify-center rounded bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:shadow-[0px_0px_1px_0px_rgba(0,0,0,0.12),0px_1px_2px_0px_rgba(0,0,0,0.24)] w-7 h-7">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21" /></svg>
                                </span>
                                Facility Booking & Scheduling
                            </span>
                        </li>
                        <li>
                            <span class="flex items-center gap-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                <span class="flex items-center justify-center rounded bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:shadow-[0px_0px_1px_0px_rgba(0,0,0,0.12),0px_1px_2px_0px_rgba(0,0,0,0.24)] w-7 h-7">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.58-3.23A2.25 2.25 0 013 9.89V8.25a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 8.25v1.64a2.25 2.25 0 01-2.84 2.05l-5.58 3.23a2.25 2.25 0 01-2.16 0z" /></svg>
                                </span>
                                Equipment Management & Tracking
                            </span>
                        </li>
                        <li>
                            <span class="flex items-center gap-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                <span class="flex items-center justify-center rounded bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:shadow-[0px_0px_1px_0px_rgba(0,0,0,0.12),0px_1px_2px_0px_rgba(0,0,0,0.24)] w-7 h-7">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" /></svg>
                                </span>
                                Maintenance Ticket System
                            </span>
                        </li>
                    </ul>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        Public facility schedule and events calendar will be available here soon.
                    </p>
                </div>
                <div class="relative -mb-px flex flex-col items-center justify-center overflow-hidden rounded-t-lg lg:rounded-t-none lg:rounded-r-lg bg-[#1b1b18] dark:bg-[#161615] p-6 lg:p-20 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                    <div class="text-center text-white">
                        <h2 class="text-2xl font-semibold mb-2">UA FMS</h2>
                        <p class="text-sm text-gray-400 mb-4">General Services Unit</p>
                        <p class="text-xs text-gray-500">Staff access via authorized launcher only</p>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
