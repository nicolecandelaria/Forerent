@extends('layouts.app')

{{-- Pass Titles to the Main Layout --}}
@section('header-title', 'PROPERTY MANAGEMENT')
@section('header-subtitle', 'View and record property or unit')

@section('content')

    @include('livewire.layouts.dashboard.admingreeting')

    {{-- Buildings Section (full width) --}}
    <div>
        <livewire:layouts.properties.building-cards-section
            :properties="$properties ?? []"
            :show-add-button="true"
            :show-add-unit-button="false"
            title="Buildings"
            add-button-event="openAddPropertyModal_property-dashboard"
        />
    </div>

    {{-- Property Details + Bed Status --}}
    <div class="mt-6 flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-[70%]">
            <livewire:layouts.properties.property-details />
        </div>
        <div class="w-full lg:w-[30%]">
            <livewire:layouts.property-widgets :initial-building-id="optional($properties->first())->property_id" />
        </div>
    </div>

    {{-- Units Section (full width) --}}
    <div class="mt-6">
        <livewire:layouts.units.unit-accordion :show-add-button="false" />
    </div>

    {{-- Modals --}}
    <livewire:layouts.properties.add-property-modal modal-id="property-dashboard" />
    <livewire:layouts.units.add-unit-modal modal-id="property-dashboard" />
    <livewire:layouts.units.landlord-contract-viewer />

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('propertyCreated', () => Livewire.dispatch('refresh-property-list'));
            Livewire.on('unitCreated', () => Livewire.dispatch('refresh-unit-list'));
        });
    </script>
    @endpush

@endsection
