<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ForeRent') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
{{--
   CRITICAL CHANGE:
   We removed "flex", "justify-center", "items-center" from this body tag.
   It is now just a plain wrapper.
--}}
<body class="font-sans text-gray-900 antialiased bg-white">

    {{-- MAIN GRID: Splits screen 50/50 on Desktop --}}
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        {{-- LEFT COLUMN: Login / Forgot Password Form --}}
        <div class="flex flex-col justify-center px-4 py-0 bg-white sm:px-6 lg:px-20 xl:px-24 h-full relative z-10">
            <div class="w-full max-w-md mx-auto">
                {{-- Livewire Content Injected Here --}}
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>

        {{-- RIGHT COLUMN: Blue Background (Hidden on mobile) --}}
        <div class="hidden lg:block relative bg-[#F4F7FC] h-full w-full">
            {{-- This empty div creates the blue side of the screen --}}
        </div>

    </div>

    @livewireScripts
</body>
</html>
