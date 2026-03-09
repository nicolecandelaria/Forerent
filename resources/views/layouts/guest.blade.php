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
<body class="font-sans text-gray-900 antialiased bg-white">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

         <div class="flex flex-col justify-center px-4 py-0 bg-white sm:px-6 lg:px-20 xl:px-24 h-full relative z-10">
            <div class="w-full max-w-md mx-auto">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>

         <div class="hidden lg:flex relative bg-[#001C64] h-full w-full items-center justify-center overflow-hidden">
             <div class="absolute inset-0 bg-gradient-to-br from-[#001C64] to-[#103FD3]"></div>

             <div class="relative w-full h-full opacity-60">
                <svg viewBox="0 0 625 170" fill="none" xmlns="http://www.w3.org/2000/svg"
                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[180%] h-auto rotate-[35deg] scale-150">
                    <path d="M234.194 74.6974L235.629 90.474C235.629 91.0256 235.187 91.5772 234.636 91.5772H212.35C211.798 91.5772 211.357 92.0185 211.357 92.5702V102.058..."
                          fill="white" fill-opacity="0.1"/>
                 </svg>
            </div>

             <div class="absolute inset-0 shadow-[inset_100px_0_100px_-50px_rgba(0,0,0,0.2)]"></div>
        </div>

    </div>

    @livewireScripts
</body>
</html>
