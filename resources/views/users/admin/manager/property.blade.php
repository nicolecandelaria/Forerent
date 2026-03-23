@extends('layouts.app')

@section('header-title', 'PROPERTY')
@section('header-subtitle', 'Centralized rental property management overview')

@section('content')
    @include('livewire.layouts.dashboard.admingreeting')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        <div class="lg:col-span-1">
            <livewire:layouts.properties.building-cards-section
                :show-add-button="auth()->user()->role === 'landlord'"
                :stacked="true"
                title="Buildings"
            />
        </div>

    {{-- Property Details Section --}}
    <div class="mt-6">
        <livewire:layouts.properties.property-details />
    </div>

    {{-- Units Section --}}
    <div class="mt-6">
        <livewire:layouts.units.unit-accordion
            :show-add-button="false"
        />
    </div>

    <livewire:layouts.properties.add-property-modal modal-id="property-dashboard" />
    <livewire:layouts.units.add-unit-modal />

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('propertyUpdated', () => Livewire.dispatch('refresh-property-list'));
            Livewire.on('unitUpdated', () => Livewire.dispatch('refresh-unit-list'));
        });
    </script>
    @endpush
@endsection
