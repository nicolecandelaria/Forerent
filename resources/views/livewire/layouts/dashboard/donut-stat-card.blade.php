<div class="bg-[#2563EB] rounded-2xl px-6 py-6 text-white shadow-lg relative overflow-hidden flex flex-row items-center justify-between gap-4 min-h-[140px]">

    {{-- Text Section --}}
    <div class="flex flex-col z-10 relative flex-1 min-w-0">
        <span class="text-sm font-medium text-blue-100 opacity-90 leading-tight break-words mb-2">
            {!! nl2br(e($title)) !!}
        </span>
        <span class="text-3xl font-bold whitespace-nowrap">&#8369; {{ number_format($amount) }}</span>
    </div>

    {{-- Donut Chart --}}
    <div class="relative w-32 h-32 sm:w-36 sm:h-36 flex-shrink-0">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
            {{-- Background Ring --}}
            <path class="text-blue-800"
                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="3"
                  stroke-opacity="0.5" />

            {{-- Progress Ring --}}
            <path class="text-white"
                  stroke-dasharray="{{ $percentage }}, 100"
                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  stroke-linecap="round" />
        </svg>

        {{-- Center Label --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-xs font-medium text-white tracking-wide">{{ $label }}</span>
        </div>
    </div>
</div>
