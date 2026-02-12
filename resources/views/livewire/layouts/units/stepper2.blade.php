{{-- This view ONLY contains the fields for Step 2 --}}
<div class="p-6 md:p-8">

    <h3 class="text-lg font-semibold text-[#021C3F] mb-6">
        Unit Amenities (for Price Prediction)
    </h3>

    <div class="p-4 rounded-lg border border-[#D1E0FF] bg-[#F7FAFF]">
        {{-- This grid has 3 columns on large screens --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- This loops over all 18 amenities from your CSV file --}}
            @foreach ($amenity_labels as $key => $label)
                <div class="flex items-center bg-white p-4 rounded-lg border border-[#E8F0FE]">
                    <input id="amenity-{{ $key }}" type="checkbox" wire:model.defer="model_amenities.{{ $key }}" class="w-4 h-4 text-[#0030C5] bg-gray-100 border-gray-300 rounded focus:ring-[#0030C5]">
                    <label for="amenity-{{ $key }}" class="ml-2 text-sm font-medium text-gray-900">{{ $label }}</label>
                </div>
            @endforeach

        </div>
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex justify-between items-center mt-8">
        <button
            wire:click="previousStep"
            class="py-2.5 px-6 font-medium text-sm rounded-lg shadow-md transition-colors duration-200 text-gray-700 bg-gray-200 hover:bg-gray-300">
            Previous
        </button>

        <button wire:click="nextStep"
            class="py-2.5 px-6 font-medium text-sm text-white bg-[#070642] rounded-lg hover:bg-[#22228e] transition-colors duration-200 shadow-md">
            Next
        </button>
    </div>
</div>
