{{-- resources/views/livewire/layouts/tenant-navigation.blade.php --}}
{{-- Using reusable list panel structure --}}

<div class="w-full bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden p-2 h-full">
    {{-- Header Section with Title and Add Button --}}
    <div class="p-4 pb-2 border-b border-gray-50 flex-shrink-0 flex items-center justify-between">
        <h3 class="text-xl font-bold text-[#070642]">Tenants</h3>
        <button
            type="button"
            x-on:click="$dispatch('open-add-tenant-modal')"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm">Add</span>
        </button>
    </div>

    {{-- List Body --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-2.5" style="scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">
        {{-- Loop through the tenants --}}
        @forelse ($tenants as $tenant)
            @php
                $isActive = ($tenant['id'] == $activeTenantId);
            @endphp

            <button
                type="button"
                wire:click="selectTenant({{ $tenant['id'] }})"
                class="cursor-pointer w-full text-left p-4 rounded-2xl transition-all duration-200 border-2
                    {{ $isActive
                        ? 'border-[#0044F1] bg-[#1679FA] shadow-md'
                        : 'border-transparent bg-white ring-1 ring-gray-100 hover:border-[#93C5FD] hover:bg-[#EEF3FF] hover:shadow-sm' }}"
            >
                <div class="flex justify-between items-start">
                    <div class="flex-1 text-left">
                        <h4 class="font-semibold text-base {{ $isActive ? 'text-white' : 'text-gray-900' }} mb-1">
                            {{ $tenant['first_name'] }} {{ $tenant['last_name'] }}
                        </h4>
                        <p class="text-xs font-bold uppercase tracking-wide {{ $isActive ? 'text-blue-100' : 'text-[#070642]' }}">
                            {{ $tenant['unit'] }} • {{ $tenant['bed_number'] }}
                        </p>
                    </div>

                    {{-- Payment Status Badge --}}
                    <div class="shrink-0 ml-2">
                        @if($tenant['payment_status'] === 'Paid')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Paid
                            </span>
                        @elseif($tenant['payment_status'] === 'Overdue')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                Overdue
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
            </button>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-16">
                <div class="bg-[#F4F7FF] p-6 rounded-full mb-4">
                    <svg class="h-10 w-10 text-[#2B66F5] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500 text-sm">No tenants found</p>
                <p class="text-xs text-gray-400 mt-1">There are currently no tenants in this property.</p>
            </div>
        @endforelse
    </div>
</div>
