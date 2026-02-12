<div>
    @if($isOpen)
        {{-- Backdrop with Blur --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm transition-opacity">

            {{-- Modal Container --}}
            <div class="relative w-full max-w-4xl bg-white rounded-[20px] shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-200">

                {{-- 1. Header (Deep Navy Blue) --}}
                <div class="bg-[#09096B] text-white px-8 py-6 relative flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold uppercase tracking-wide">ADD MAINTENANCE REQUEST</h2>
                        <p class="text-xs text-blue-200 opacity-90 mt-1">Fill in the details to submit a new ticket</p>
                    </div>

                    {{-- Close Button --}}
                    <button
                        type="button"
                        x-on:click="$dispatch('open-modal', 'discard-maintenance-confirmation')"
                        class="text-white/80 hover:text-white hover:bg-white/10 rounded-full p-1 transition-all"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="p-8 space-y-6 overflow-y-auto max-h-[65vh]">

                    {{-- 2. Summary Card --}}
                    <div class="bg-[#2672EC] rounded-xl p-6 text-white flex justify-between items-center shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10 pointer-events-none"></div>

                        <div class="space-y-1 relative z-10">
                            <p class="text-xs font-light tracking-wide opacity-90">{{ $residentName }}</p>
                            <h3 class="text-3xl font-bold leading-tight">{{ $unitNumber }}</h3>
                            <p class="text-sm font-medium opacity-90">{{ $buildingName }}</p>
                        </div>

                        <div class="text-right space-y-3 relative z-10">
                            <div>
                                <p class="text-[10px] opacity-70 uppercase tracking-widest font-bold">Reported Date</p>
                                <p class="text-sm font-bold">{{ now()->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] opacity-70 uppercase tracking-widest font-bold">Ticket Number</p>
                                <p class="text-sm font-bold">{{ $ticketNumber }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Category Selection --}}
                    <div>
                        <label class="block text-[#333333] font-bold mb-3 text-sm">Category</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['Plumbing', 'Electrical', 'HVAC', 'Appliance', 'Other'] as $cat)
                                <button
                                    type="button"
                                    wire:click="selectCategory('{{ $cat }}')"
                                    class="px-6 py-2.5 rounded-lg border text-sm font-semibold transition-all shadow-sm
                                    {{ $category === $cat
                                        ? 'bg-[#2672EC] border-[#2672EC] text-white ring-2 ring-blue-200 ring-offset-1'
                                        : 'bg-white border-gray-200 text-[#2672EC] hover:bg-blue-50 hover:border-blue-300'
                                    }}"
                                >
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. Issue Description --}}
                    <div class="relative">
                        <textarea
                            wire:model.defer="description"
                            id="description-{{ $modalId }}"
                            class="block px-2.5 pb-2.5 pt-6 w-full h-32 text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#2672EC] peer resize-none"
                            placeholder=" "
                        ></textarea>

                        <label
                            for="description-{{ $modalId }}"
                            class="absolute text-m text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#2672EC] peer-placeholder-shown:scale-100 peer-placeholder-shown:top-10 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1 cursor-text"
                        >
                            Issue Description
                        </label>

                        @error('description')
                            <span class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{--  Image Upload --}}
                    <div>
                        <label class="block text-[#333333] font-bold mb-2 text-sm">Image</label>

                        <div class="relative border-2 border-dashed border-[#2672EC]/40 rounded-xl p-8 bg-[#F0F5FF] text-center group hover:bg-[#E6EEFF] transition-colors cursor-pointer">
                            <input
                                type="file"
                                wire:model="image"
                                accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            >

                            @if ($image)
                                <div class="flex flex-col items-center">
                                    <img src="{{ $image->temporaryUrl() }}" class="h-20 w-auto rounded-lg shadow-sm mb-2 object-cover">
                                    <p class="text-xs text-[#2672EC] font-bold">Click to change</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center pointer-events-none">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-6 h-6 text-[#2672EC]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">Drag and Drop to Upload</p>
                                    <p class="text-xs text-gray-400 mt-1">or <span class="text-[#2672EC] font-bold hover:underline">Browse</span></p>
                                </div>
                            @endif
                        </div>
                        @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- 6. Footer --}}
                <div class="p-8 pt-0 flex justify-end">
                    <button
                        type="button"
                        wire:click="validateAndConfirm"
                        class="bg-[#09096B] hover:bg-[#06064a] text-white font-bold px-10 py-3 rounded-full transition-all shadow-lg hover:shadow-xl active:scale-95 text-sm flex items-center gap-2"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save">Save</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Confirmation & Cancel Modals --}}
        <x-ui.modal-confirm
            name="save-maintenance-confirmation"
            title="Submit Maintenance Request?"
            description="Are you sure you want to submit this request? The maintenance team will be notified immediately."
            confirmText="Yes, Save"
            cancelText="Cancel"
            confirmAction="save"
        />

        <x-ui.modal-cancel
            name="discard-maintenance-confirmation"
            title="Discard Unsaved Changes?"
            description="Are you sure you want to close? All details entered will be lost."
            discardText="Discard"
            returnText="Keep Editing"
            discardAction="close"
        />
    @endif
</div>
