<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    {{-- Calendar Left Side (Compact) --}}
    <div class="bg-white rounded-xl shadow-md p-4 lg:col-span-1">
        {{-- Calendar Header with Navigation --}}
        <div class="flex items-center justify-between mb-4">
            <button
                wire:click="previousMonth"
                type="button"
                class="p-1 hover:bg-gray-100 rounded transition-colors"
                title="Previous month"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <h3 class="text-sm font-bold text-gray-900 text-center flex-1">{{ $currentMonth }}</h3>

            <button
                wire:click="nextMonth"
                type="button"
                class="p-1 hover:bg-gray-100 rounded transition-colors"
                title="Next month"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Return to Today Button --}}
        @if(\Carbon\Carbon::parse($selectedDate)->format('Y-m') !== \Carbon\Carbon::now()->format('Y-m'))
        <button
            wire:click="currentMonth"
            type="button"
            class="w-full mb-3 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded px-2 py-1 transition-colors"
            title="Return to today"
        >
            Today
        </button>
        @endif

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7 gap-1 text-center">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
            <div class="text-xs font-medium text-gray-600 pb-1">{{ $day }}</div>
            @endforeach

                @foreach($calendarDays as $day)
                    @if($day === null)
                        <div class="aspect-square"></div>
                    @else
                        <button
                            wire:click="selectDate('{{ $currentYear }}-{{ date('m', strtotime($currentMonth)) }}-{{ $day }}')"
                            class="aspect-square flex flex-col items-center justify-center rounded text-xs transition-all
                {{ \Carbon\Carbon::parse($selectedDate)->day == $day && \Carbon\Carbon::parse($selectedDate)->format('m') == date('m', strtotime($currentMonth))
                    ? 'bg-blue-700 text-white font-bold'
                    : 'hover:bg-gray-100 text-gray-700' }}">
                            <span>{{ $day }}</span>

                            {{-- Dot for days with announcements --}}
                            @if(in_array($day, $announcementDates))
                                <span class="w-1.5 h-1.5 mt-0.5 rounded-full
                    {{ \Carbon\Carbon::parse($selectedDate)->day == $day && \Carbon\Carbon::parse($selectedDate)->format('m') == date('m', strtotime($currentMonth))
                        ? 'bg-white'
                        : 'bg-blue-700' }}">
                </span>
                            @endif
                        </button>
                    @endif
                @endforeach
        </div>
    </div>

    {{-- Daily Events Right Side --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden lg:col-span-3 flex flex-col">
        <div class="bg-blue-700 px-6 py-4">
            <h3 class="text-white text-lg font-semibold">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</h3>
        </div>
        <div class="flex-1 flex flex-col">
            <div class="p-6 space-y-4 h-80 overflow-y-scroll custom-scrollbar flex-1">
                @forelse($dailyAnnouncements as $dailyAnnouncement)
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <h4 class="text-base font-bold text-gray-900 mb-1">{{ $dailyAnnouncement->headline }}</h4>
                        <p class="text-sm text-gray-600">{{ $dailyAnnouncement->details }}</p>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center">
                        <p class="text-gray-500 text-center">No Events for Today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
