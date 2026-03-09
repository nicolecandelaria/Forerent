<div
    x-data="{ show: false }"
    x-show="show"
    @open-create-maintenance-modal.window="show = true; $wire.dispatch('reset-modal')"
    @close-modal.window="show = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="show = false"></div>

    {{-- Modal --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden z-50 font-sans"
    >
        {{-- Header --}}
        <div class="bg-[#2B66F5] px-6 py-5 text-white flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold">New Maintenance Request</h3>
                <p class="text-xs text-blue-200 mt-0.5">Fill in the details below</p>
            </div>
            <button @click="show = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form Body --}}
        <div class="p-6 space-y-5">

            {{-- Category --}}
            <div>
                <label class="block text-sm font-bold text-[#070642] mb-2">Category</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Plumbing', 'Electrical', 'Structural', 'Appliance', 'Pest Control'] as $cat)
                        <button
                            type="button"
                            wire:click="$set('category', '{{ $cat }}')"
                            class="px-4 py-2 rounded-xl border text-xs font-semibold transition-all
                                {{ $category === $cat
                                    ? 'bg-[#2B66F5] border-[#2B66F5] text-white shadow-sm'
                                    : 'bg-white border-gray-200 text-gray-600 hover:border-blue-300 hover:text-[#2B66F5]'
                                }}"
                        >
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
                @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Urgency --}}
            <div>
                <label for="urgency" class="block text-sm font-bold text-[#070642] mb-2">Priority Level</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach([
                        'Level 1' => ['label' => 'Level 1', 'desc' => 'Critical', 'color' => 'red'],
                        'Level 2' => ['label' => 'Level 2', 'desc' => 'High',     'color' => 'orange'],
                        'Level 3' => ['label' => 'Level 3', 'desc' => 'Medium',   'color' => 'yellow'],
                        'Level 4' => ['label' => 'Level 4', 'desc' => 'Low',      'color' => 'green'],
                    ] as $value => $info)
                        <button
                            type="button"
                            wire:click="$set('urgency', '{{ $value }}')"
                            class="flex flex-col items-center p-3 rounded-xl border-2 text-xs font-semibold transition-all
                                {{ $urgency === $value
                                    ? 'border-[#2B66F5] bg-[#EEF3FF] text-[#2B66F5]'
                                    : 'border-gray-200 bg-white text-gray-500 hover:border-blue-200'
                                }}"
                        >
                            <span class="font-bold text-sm">{{ $info['label'] }}</span>
                            <span class="text-[10px] opacity-70 mt-0.5">{{ $info['desc'] }}</span>
                        </button>
                    @endforeach
                </div>
                @error('urgency') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Problem Description --}}
            <div>
                <label for="problem" class="block text-sm font-bold text-[#070642] mb-2">Issue Description</label>
                <textarea
                    wire:model="problem"
                    id="problem"
                    rows="5"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2B66F5] bg-gray-50 resize-none placeholder-gray-400 text-sm"
                    placeholder="Please describe the issue in detail (at least 10 characters)..."
                ></textarea>
                @error('problem') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    More detail helps us resolve your issue faster.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button
                @click="show = false"
                type="button"
                class="px-5 py-2.5 rounded-xl font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors text-sm"
            >
                Cancel
            </button>
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                type="button"
                class="px-6 py-2.5 rounded-xl font-bold text-white bg-[#2B66F5] hover:bg-[#1a4fd1] transition-colors flex items-center gap-2 disabled:opacity-60 text-sm shadow-sm"
            >
                <span wire:loading.remove wire:target="save">Submit Request</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    Submitting...
                </span>
            </button>
        </div>
    </div>
</div>
