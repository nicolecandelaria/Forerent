<div>
    {{-- Main Announcement Form Modal --}}
    @if($showModal && !$showConfirmation)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Modal Panel --}}
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">

            {{-- 1. Header Section (Matched Color #070589) --}}
            <div class="bg-[#070589] text-white p-6 flex-shrink-0">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold uppercase">ANNOUNCEMENT</h2>
                        <p class="mt-1 text-sm text-blue-100">Fill in the details to post a new update</p>
                    </div>
                    {{-- Close Button --}}
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="text-white hover:text-blue-200 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- 2. Form Body --}}
            <div class="flex-1 overflow-y-auto p-8">
                <div class="space-y-6">

                    {{-- White Container Card --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">

                        {{-- Headline (Floating Label) --}}
                        <div class="relative">
                            <input
                                wire:model="headline"
                                type="text"
                                id="headline"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#0030C5] peer placeholder-transparent"
                                placeholder=" "
                            />
                            <label
                                for="headline"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#0030C5] peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1 pointer-events-none"
                            >
                                Headline
                            </label>
                            @error('headline')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Details (Floating Label Textarea) --}}
                        <div class="relative">
                            <textarea
                                wire:model="details"
                                id="details"
                                rows="5"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#0030C5] peer resize-none placeholder-transparent"
                                placeholder=" "
                            ></textarea>
                            <label
                                for="details"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#0030C5] peer-placeholder-shown:scale-100 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1 pointer-events-none"
                            >
                                Details
                            </label>
                            @error('details')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Property (Floating Label Textarea) --}}
                        <div class="relative">
                            <select
                                wire:model="propertyId"
                                id="propertyId"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-[#0030C5] peer"
                            >
                                <option value="" {{ is_null($propertyId) ? 'selected' : '' }} hidden>
                                    Select a property
                                </option>

                                @foreach ($properties as $property)
                                    <option value="{{ $property->property_id }}">
                                        {{ $property->building_name }}
                                    </option>
                                @endforeach
                            </select>
                            <label
                                for="propertyId"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-[#0030C5] peer-placeholder-shown:scale-100 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1 pointer-events-none"
                            >
                                Property
                            </label>
                            @error('propertyId')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- 3. Footer --}}
            <div class="p-6 pt-0 flex justify-end">
                <button
                    wire:click="save"
                    type="button"
                    class="px-8 py-3 bg-[#070589] text-white text-sm font-semibold rounded-lg hover:bg-[#001445] focus:ring-4 focus:ring-blue-300 transition-colors shadow-lg">
                    Save
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmation Modal --}}
    @if($showConfirmation)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 text-center">
                {{-- Updated Title Color --}}
                <h3 class="text-2xl font-bold text-[#070589] mb-4">Post Announcement?</h3>
                <p class="text-gray-600 mb-8">
                    Your announcement will be saved and will be visible to everyone immediately. Do you want to proceed?
                </p>
                <div class="flex gap-4 justify-center">
                    <button
                        wire:click="cancelConfirmation"
                        type="button"
                        class="px-6 py-2.5 bg-blue-50 text-[#070589] font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                        Cancel
                    </button>
                    <button
                        wire:click="confirmPost"
                        type="button"
                        class="px-6 py-2.5 bg-[#070589] text-white font-semibold rounded-lg hover:bg-[#001445] transition-colors shadow-lg">
                        Confirm Post
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-announcement-modal', () => {
            @this.call('openModal');
        });
    });
</script>
@endpush
