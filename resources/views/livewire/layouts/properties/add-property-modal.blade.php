<div>
    @if($isOpen)
        {{-- Main Add Property Modal  --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="relative w-full max-w-2xl bg-gray-50 rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">

                <div class="bg-[#070589] text-white p-6 flex-shrink-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold uppercase">
                                {{ $editingPropertyId ? 'EDIT PROPERTY' : 'ADD NEW PROPERTY' }}
                            </h2>
                            <p class="mt-1 text-sm text-blue-100">
                                {{ $editingPropertyId ? 'Update property details' : 'Fill in the details to predict rental price' }}
                            </p>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('open-modal', 'discard-property-confirmation')"
                            class="text-white hover:text-blue-200 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>

                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <div class="space-y-8">

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-8">

                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <h3 class="text-base font-semibold text-gray-900">Unit Identification</h3>
                                </div>

                                <p class="text-sm text-gray-600 mb-6">
                                    Basic property information collected. Unit details will be added in the next step.
                                </p>

                                <!-- Property Name -->
                                <div class="relative mb-6">
                                    <input
                                        wire:model.defer="buildingName"
                                        type="text"
                                        id="buildingName"
                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#0030C5] peer"
                                        placeholder=" "
                                    />
                                    <label
                                        for="buildingName"
                                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#0030C5] peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1"
                                    >
                                        Property Name
                                    </label>
                                    @error('buildingName')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="relative mb-6">
                                    <input
                                        wire:model.defer="address"
                                        type="text"
                                        id="address"
                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#0030C5] peer"
                                        placeholder=" "
                                    />
                                    <label
                                        for="address"
                                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#0030C5] peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1"
                                    >
                                        Address
                                    </label>
                                    @error('address')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="relative">
                                    <textarea
                                        wire:model.defer="description"
                                        id="description"
                                        rows="4"
                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#0030C5] peer resize-none"
                                        placeholder=" "
                                    ></textarea>
                                    <label
                                        for="description"
                                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#0030C5] peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1"
                                    >
                                        Description
                                    </label>
                                    @error('description')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="flex justify-end pt-4">
                            <button
                                type="button"
                                wire:click="$dispatch('open-modal', 'save-property-confirmation')"
                                class="px-8 py-3 bg-[#070589] text-white text-sm font-semibold rounded-lg hover:bg-[#001445] focus:ring-4 focus:ring-blue-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="next">Save</span>
                                <span wire:loading wire:target="next">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 1. SAVE CONFIRMATION  --}}
        <x-ui.modal-confirm
            name="save-property-confirmation"
            title="Save Property?"
            description="Are you sure you want to add this new property?"
            confirmText="Yes, Save"
            cancelText="Cancel"
            confirmAction="next"
        />

        {{-- 2. DISCARD CONFIRMATION --}}
        <div
            x-data="{ show: false }"
            x-show="show"
            x-on:open-modal.window="if ($event.detail === 'discard-property-confirmation') show = true"
            x-on:close-modal.window="if ($event.detail === 'discard-property-confirmation') show = false"
            x-on:keydown.escape.window="show = false"
            class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-6 sm:px-0"
            style="display: none;"
        >
            <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false">
                <div class="absolute inset-0 bg-gray-600 opacity-50"></div>
            </div>

            <div x-show="show" class="bg-white rounded-[20px] overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-[480px] p-8 relative z-[100]">
                <button @click="show = false" class="absolute top-5 right-5 text-[#0C0B50] hover:text-blue-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="text-center mt-4 mb-8">
                    <h3 class="text-2xl font-bold text-[#0C0B50] mb-3">Discard Unsaved Changes?</h3>
                    <p class="text-gray-500 text-sm leading-relaxed px-4">Are you sure you want to close? All details will be lost.</p>
                </div>

                <div class="flex justify-center gap-4 px-2">
                    <button
                        wire:click="close"
                        class="flex-1 bg-[#D6E6FF] hover:bg-[#c3daff] text-[#0C0B50] font-bold py-3 rounded-xl transition-colors text-sm">
                        Discard
                    </button>

                    <button
                        @click="show = false"
                        class="flex-1 bg-[#104EA2] hover:bg-[#0d3f82] text-white font-bold py-3 rounded-xl transition-colors shadow-md text-sm">
                        Keep Editing
                    </button>
                </div>
            </div>
        </div>

    @endif
</div>
