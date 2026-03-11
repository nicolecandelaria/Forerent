{{-- Generic reusable list panel --}}
<div class="w-full bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden p-2 h-full">

    {{-- List Header --}}
    <div class="p-4 pb-2 border-b border-gray-50 flex-shrink-0">
        <h3 class="text-xl font-bold text-[#070642]">{{ $title }}</h3>
    </div>

    {{-- List Body --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-2.5" style="scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">
        @forelse($items as $item)
            {{-- Slot for custom item rendering per page --}}
            <slot name="item" :item="$item" :isActive="$this->parentComponent->{$this->activeIdProperty} === $item->{$this->itemKey}">
                {{-- Default fallback (can be overridden) --}}
                <div class="p-4 rounded-2xl bg-white border border-gray-200">
                    <p class="text-sm text-gray-600">{{ $item->name ?? 'Item' }}</p>
                </div>
            </slot>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-16">
                <div class="bg-[#F4F7FF] p-6 rounded-full mb-4">
                    <svg class="h-10 w-10 text-[#2B66F5] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500 text-sm">{{ $emptyTitle }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $emptyDescription }}</p>
            </div>
        @endforelse
    </div>
</div>
