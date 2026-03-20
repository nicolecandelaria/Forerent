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
    <style>
        .building-scroll {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .building-scroll::-webkit-scrollbar {
            height: 4px;
            background: transparent;
        }
        .building-scroll::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 999px;
        }
        .building-scroll::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 999px;
            transition: background 0.3s;
        }
        .building-scroll:hover {
            scrollbar-width: thin;
            scrollbar-color: rgba(180, 180, 180, 0.5) transparent;
        }
        .building-scroll:hover::-webkit-scrollbar-thumb {
            background: rgba(180, 180, 180, 0.6);
        }
        .building-scroll:hover::-webkit-scrollbar-thumb:hover {
            background: rgba(150, 150, 150, 0.7);
        }

        /* Selected building card */
        .selected-building > div {
            background: linear-gradient(135deg, #1D56D9 0%, #2360E8 50%, #2D6FF5 100%) !important;
            box-shadow: 0 4px 20px rgba(35, 96, 232, 0.35);
        }
        .selected-building .building-card-title,
        .selected-building .building-card-address {
            color: white !important;
        }
    </style>

    <div
        x-data="{
            scrollContainer: null,
            canScrollLeft: false,
            canScrollRight: false,
            init() {
                this.scrollContainer = this.$refs.scroller;
                this.checkScroll();
                this.scrollContainer.addEventListener('scroll', () => this.checkScroll());
                new ResizeObserver(() => this.checkScroll()).observe(this.scrollContainer);
            },
            checkScroll() {
                const el = this.scrollContainer;
                this.canScrollLeft = el.scrollLeft > 0;
                this.canScrollRight = el.scrollLeft < el.scrollWidth - el.clientWidth - 1;
            },
            scrollBy(direction) {
                this.scrollContainer.scrollBy({ left: direction * 300, behavior: 'smooth' });
            }
        }"
        class="group/arrows relative"
    >
        {{-- Left Arrow --}}
        <button
            x-show="canScrollLeft"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="scrollBy(-1)"
            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 shadow-md border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-white hover:text-[#2360E8] hover:border-[#2360E8]/30 transition-all opacity-0 group-hover/arrows:opacity-100 cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
        </button>

        {{-- Right Arrow --}}
        <button
            x-show="canScrollRight"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="scrollBy(1)"
            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 shadow-md border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-white hover:text-[#2360E8] hover:border-[#2360E8]/30 transition-all opacity-0 group-hover/arrows:opacity-100 cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
        </button>

        {{-- Scrollable Container --}}
        <div x-ref="scroller" class="building-scroll flex gap-4 overflow-x-auto py-2 pb-4">
            @forelse ($properties as $property)
                <div
                    wire:key="building-{{ $property->property_id }}"
                    wire:click="selectBuilding({{ $property->property_id }})"
                    class="cursor-pointer rounded-lg transition-all duration-300 {{ $selectedBuilding == $property->property_id ? 'selected-building' : 'hover:shadow-lg' }}"
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
</div>
