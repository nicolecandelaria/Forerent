{{-- resources/views/livewire/layouts/tenant-navigation.blade.php --}}

<div class="w-full bg-white p-4 md:p-6 rounded-2xl shadow-md h-full flex flex-col">
    {{-- Header Section with Title and Add Button --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Tenants</h2>
        <button
    type="button"
    {{-- CHANGED: Use a unique event name with NO parameters to prevent type errors --}}
    x-on:click="$dispatch('open-add-tenant-modal')"
    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
    </svg>
    Add Tenant
</button>
    </div>

    {{-- Tenant List Container --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-2 space-y-3">
        {{-- Loop through the tenants --}}
        @forelse ($tenants as $tenant)
            @php
                $baseClasses = 'w-full text-left font-semibold p-4 rounded-lg border-2 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400';
                $isActive = ($tenant['id'] == $activeTenantId);

                if ($isActive) {
                    $buttonClasses = 'bg-blue-600 text-white border-blue-600 shadow-lg';
                } else {
                    $buttonClasses = 'bg-white text-gray-700 border-gray-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-500';
                }
            @endphp

            <button
                type="button"
                wire:click="selectTenant({{ $tenant['id'] }})"
                class="{{ $baseClasses }} {{ $buttonClasses }}"
            >
                <div class="flex justify-between items-start">
                    <div class="flex-1 text-left">
                        <h4 class="font-semibold text-base mb-1">{{ $tenant['name'] }}</h4>
                        <p class="text-sm opacity-90">{{ $tenant['unit'] }} • {{ $tenant['bed_number'] }}</p>
                    </div>

                    {{-- Payment Status Badge --}}
                    <div class="flex-shrink-0 ml-2">
                        @if($tenant['payment_status'] === 'Paid')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Paid
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
            </button>
        @empty
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <p class="text-gray-500 mb-4">No tenants found.</p>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
                >
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Your First Tenant
                </button>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #0039C6;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #002A8F;
    }
</style>
@endpush
