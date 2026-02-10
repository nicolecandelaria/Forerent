<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Calendar Left Side --}}
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $currentMonth }}</h3>
        <div class="grid grid-cols-7 gap-2 text-center">
            @foreach(['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'] as $day)
            <div class="text-xs font-medium text-gray-600 pb-2">{{ $day }}</div>
            @endforeach

                @foreach($calendarDays as $day)
                    @if($day === null)
                        <div class="aspect-square"></div>
                    @else
                        <button
                            wire:click="selectDate('{{ $currentYear }}-{{ date('m', strtotime($currentMonth)) }}-{{ $day }}')"
                            class="aspect-square flex flex-col items-center justify-center rounded-lg text-sm
                {{ \Carbon\Carbon::parse($selectedDate)->day == $day
                    ? 'bg-blue-700 text-white font-bold'
                    : 'hover:bg-gray-100 text-gray-700' }}">
                            <span>{{ $day }}</span>

                            {{-- Dot for days with announcements --}}
                            @if(in_array($day, $announcementDates))
                                <span class="w-2 h-2 mt-1 rounded-full
                    {{ \Carbon\Carbon::parse($selectedDate)->day == $day
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
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-blue-700 px-6 py-4">
            <h3 class="text-white text-lg font-semibold">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</h3>
        </div>
        <div class="p-6 space-y-4 max-h-80 overflow-y-auto">
            @forelse($dailyAnnouncements as $dailyAnnouncement)
            <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                <h4 class="text-base font-bold text-gray-900 mb-1">{{ $dailyAnnouncement->headline }}</h4>
                <p class="text-sm text-gray-600">{{ $dailyAnnouncement->description }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No events for this day</p>
            @endforelse
        </div>
    </div>
</div>
