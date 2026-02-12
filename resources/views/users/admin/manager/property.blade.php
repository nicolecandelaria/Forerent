@extends('layouts.app')

@section('header-title', 'PROPERTY')
@section('header-subtitle', 'Centralized rental property management overview')

@section('content')

    @include('livewire.layouts.dashboard.admingreeting')

    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif


    <livewire:layouts.properties.building-cards-section
        :show-add-button="false"
        title="Buildings"
    />


    <div class="mt-6">
        <livewire:layouts.units.unit-accordion
            :show-add-button="false"
        />
    </div>


    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('propertyUpdated', () => Livewire.dispatch('refresh-property-list'));
            Livewire.on('unitUpdated', () => Livewire.dispatch('refresh-unit-list'));
        });
    </script>
    @endpush

    
@endsection
