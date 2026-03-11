<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-900">{{ $title }}</h3>

        @if($showAddButton)
            <button
                type="button"
                onclick="Livewire.dispatch('{{ $addButtonEvent }}')"
                class="py-2 px-4 text-sm font-medium text-white bg-[#2360E8] rounded-lg hover:bg-[#1d4eb8] transition-colors">
                + Add Property
            </button>
        @endif
    </div>

    {{-- Horizontal Scroll Cards --}}
    <div class="flex gap-4 overflow-x-auto pb-4
                [&::-webkit-scrollbar]:hidden
                [-ms-overflow-style:none]
                [scrollbar-width:none]">

        @forelse ($properties as $property)
            <div
                wire:key="building-{{ $property->property_id }}"
                wire:click="selectBuilding({{ $property->property_id }})"
                class="cursor-pointer rounded-lg"
            >
                <livewire:layouts.properties.buildings
                    :property="$property"
                    :key="'card-'.$property->property_id"
                />
            </div>
        @empty
            <div class="w-full flex flex-col items-center justify-center text-center p-16 border-2 border-dashed border-gray-300 rounded-lg bg-white">
                <h3 class="text-xl font-semibold text-gray-700">
                    {{ $emptyStateTitle }}
                </h3>
                <p class="text-gray-500 mt-2">
                    {{ $emptyStateDescription }}
                </p>

                @if($showAddButton)
                    <button
                        type="button"
                        onclick="Livewire.dispatch('{{ $addButtonEvent }}')"
                        class="mt-4 py-2 px-6 text-sm font-medium text-white bg-[#2360E8] rounded-lg hover:bg-[#1d4eb8] transition-colors">
                        Add Your First Property
                    </button>
                @endif
            </div>
        @endforelse
    </div>
</div>
