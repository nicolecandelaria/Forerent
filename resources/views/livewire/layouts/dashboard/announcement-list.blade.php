<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-blue-800 px-6 py-4 flex justify-between items-center">
        <h3 class="text-white text-lg font-semibold">Announcement</h3>

       {{-- Show Add button for Landlord and Manager only --}}
        @if(request()->is('landlord') || request()->is('manager'))
            <button
                wire:click="$dispatch('open-announcement-modal')"
                type="button"
                class="text-white hover:text-gray-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        @endif
    </div>
    <div class="p-6 space-y-4 max-h-64 overflow-y-auto">
        @forelse($announcements as $announcement)
        <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
            <div class="text-sm text-blue-700 font-semibold mb-1">{{ $announcement['created_at'] }}</div>
            <h4 class="text-base font-bold text-gray-900 mb-1">{{ $announcement['headline'] }}</h4>
            <p class="text-sm text-gray-600">{{ $announcement['details'] }}</p>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4">No announcements yet</p>
        @endforelse
    </div>
</div>
