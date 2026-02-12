<div>
    @if($isOpen)
        {{-- Main Add Unit Modal --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="relative w-full max-w-4xl bg-gray-50 rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">

                <div class="bg-[#070589] text-white p-6 flex-shrink-0">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold uppercase">
                {{ $editingUnitId ? 'EDIT UNIT' : 'ADD NEW UNIT' }}
            </h2>
            <p class="mt-1 text-sm text-blue-100">
                {{ $editingUnitId ? 'Update unit details and specifications' : 'Fill in the details to predict rental price' }}
            </p>
        </div>
        </div>
</div>

                <div class="flex-1 overflow-y-auto">
                    <ol class="flex items-start justify-between w-full pt-4 pb-6 px-6">
                        @foreach ($steps as $step => $label)
                            @php
                                $isActive = $currentStep == $step;
                                $isLast = $step == count($steps);
                            @endphp

                            <li class="flex flex-col items-center flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $isActive ? 'bg-[#070642] text-white' : 'bg-gray-200 text-gray-700' }}">
                                    <span class="font-medium">{{ $step }}</span>
                                </div>
                                <span class="mt-2 text-sm text-center
                                    {{ $isActive ? 'text-[#070642] font-bold' : 'text-gray-400 font-normal' }}">
                                    {{ $label }}
                                </span>
                            </li>

                            @if(!$isLast)
                                <li class="flex-auto h-0.5 bg-gray-200 mt-4 mx-2"></li>
                            @endif
                        @endforeach
                    </ol>

                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mx-6 mb-6">
                        @if ($currentStep == 1)
                            @include('livewire.layouts.units.stepper1')
                        @elseif ($currentStep == 2)
                            @include('livewire.layouts.units.stepper2')
                        @elseif ($currentStep == 3)
                            @include('livewire.layouts.units.stepper3')
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div
            x-data="{ show: false }"
            x-show="show"
            x-on:open-modal.window="if ($event.detail === 'discard-unit-confirmation') show = true"
            x-on:close-modal.window="if ($event.detail === 'discard-unit-confirmation') show = false"
            x-on:keydown.escape.window="show = false"
            class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-6 sm:px-0"
            style="display: none;"
        >
            {{-- Overlay --}}
            <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false">
                <div class="absolute inset-0 bg-gray-600 opacity-50"></div>
            </div>

            {{-- Modal Content --}}
            <div x-show="show" class="bg-white rounded-[20px] overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-[480px] p-8 relative z-[100]">
                <button @click="show = false" class="absolute top-5 right-5 text-[#0C0B50] hover:text-blue-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="text-center mt-4 mb-8">
                    <h3 class="text-2xl font-bold text-[#0C0B50] mb-3">Discard Unsaved Changes?</h3>
                    <p class="text-gray-500 text-sm leading-relaxed px-4">Are you sure you want to close? All details will be lost.</p>
                </div>

                <div class="flex justify-center gap-4 px-2">
                    {{-- DISCARD BUTTON: This actually closes the main modal --}}
                    <button
                        wire:click="close"
                        class="flex-1 bg-[#D6E6FF] hover:bg-[#c3daff] text-[#0C0B50] font-bold py-3 rounded-xl transition-colors text-sm">
                        Discard
                    </button>

                    {{-- KEEP EDITING BUTTON: Just hides this popup --}}
                    <button
                        @click="show = false"
                        class="flex-1 bg-[#104EA2] hover:bg-[#0d3f82] text-white font-bold py-3 rounded-xl transition-colors shadow-md text-sm">
                        Keep Editing
                    </button>
                </div>
            </div>
        </div>

        <x-ui.modal-cancel
            name="discard-unit-confirmation"
            title="Discard Unsaved Changes?"
            description="Are you sure you want to close? All details entered will be lost."
            discardText="Discard"
            returnText="Keep Editing"
            discardAction="close"
        />
        
    @endif
</div>
