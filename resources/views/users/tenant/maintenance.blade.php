@extends('layouts.app')
@section('header-title', 'MAINTENANCE MANAGEMENT')
@section('header-subtitle', 'Monitor costs, track trends, and manage repair tickets')

@section('content')
<div class="p-6 w-full h-[calc(100vh-80px)] flex flex-col font-['Poppins'] overflow-hidden"
     x-data="{ activeTab: 'all' }">

    {{-- 1. HEADER & BANNER SECTION --}}
    <div class="flex-shrink-0 mb-6">
        <div class="w-full bg-gradient-to-r from-[#070642] to-[#1a4fd1] rounded-3xl p-8 text-white shadow-lg relative overflow-hidden">
            <h2 class="text-3xl font-medium relative z-10">Hello, {{ Auth::user()->first_name ?? 'Tenant' }}!</h2>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="absolute bottom-0 right-20 w-32 h-32 bg-white opacity-5 rounded-full mb-[-40px] pointer-events-none"></div>
        </div>
    </div>

    {{-- 2. TABS & ACTIONS ROW --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 flex-shrink-0 gap-4">

        {{-- Tabs --}}
        <div class="flex items-center gap-8 border-b border-gray-200 w-full md:w-auto overflow-x-auto">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'ongoing' => 'On Hold', 'completed' => 'Completed'] as $key => $label)
                <button
                    @click="activeTab = '{{ $key }}'; $dispatch('filter-maintenance', { status: '{{ $key }}' })"
                    :class="activeTab === '{{ $key }}' ? 'text-[#070642] border-b-4 border-[#070642]' : 'text-gray-400 border-transparent hover:text-gray-600'"
                    class="pb-2 text-lg font-bold transition-all whitespace-nowrap px-2">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{--
            FIX APPLIED HERE:
            1. Removed wire:click (invalid in standard blade files)
            2. Added @click="$dispatch('open-maintenance-modal')" to trigger the event via Alpine
        --}}
        <x-ui.button-add
            href="#"
            text="Add Maintenance Request"
            @click="$dispatch('open-maintenance-modal')"
            class="bg-[#070642] hover:bg-[#1a1955]"
        />
    </div>

    {{-- 3. MAIN CONTENT GRID --}}
    <div class="flex flex-col lg:flex-row gap-6 flex-1 min-h-0">
        {{-- LEFT PANEL: LIST --}}
        <div class="w-full lg:w-1/3 h-full bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden p-2">
            <div class="p-4 pb-2">
                <h3 class="text-xl font-bold text-[#070642]">Maintenance Request</h3>
            </div>
            <livewire:layouts.maintenance.tenant-maintenance-list />
        </div>

        {{-- RIGHT PANEL: DETAIL --}}
        <div class="w-full lg:w-2/3 h-full bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col p-2">
            <livewire:layouts.maintenance.tenant-maintenance-detail />
        </div>

        {{-- Modals --}}
        <livewire:layouts.maintenance.add-maintenance-modal />
    </div>
</div>
@endsection
