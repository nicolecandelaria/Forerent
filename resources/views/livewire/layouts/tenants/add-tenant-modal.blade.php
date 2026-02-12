<div>
    @if($isOpen)
        {{-- Modal Backdrop --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="relative w-full max-w-3xl bg-gray-50 rounded-2xl shadow-xl overflow-hidden max-h-[95vh] flex flex-col">

                {{-- Header --}}
                <div class="bg-[#070589] text-white p-6 flex-shrink-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold uppercase">ADD NEW TENANT</h2>
                            <p class="mt-1 text-sm text-blue-100">Fill in the details to add new tenant</p>
                        </div>
                        {{-- Close Button (Triggers Discard Modal) --}}
                        <button
                            type="button"
                            x-on:click="$dispatch('open-modal', 'discard-tenant-confirmation')"
                            class="text-white hover:text-blue-200 transition-colors focus:outline-none"
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Scrollable Content --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mx-6 my-6 p-8">
                        <form wire:submit.prevent="save" class="space-y-8">

                            {{-- SECTION 1: Profile Information --}}
                            <div>
                                <h3 class="text-base font-bold text-[#070589] flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    Profile Information
                                </h3>

                                <div class="flex items-start gap-6 mb-6">
                                    {{-- Profile Picture --}}
                                    <div class="flex-shrink-0">
                                        <label class="cursor-pointer group relative block w-24 h-24">
                                            @if ($profilePicture)
                                                <img src="{{ $profilePicture->temporaryUrl() }}" class="w-full h-full rounded-full object-cover border-4 border-[#001B5E]">
                                            @else
                                                <div class="w-full h-full rounded-full bg-[#001B5E] flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                                </div>
                                            @endif
                                            <div class="absolute bottom-0 right-0 bg-white rounded-full p-1 border shadow">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            </div>
                                            <input type="file" wire:model="profilePicture" class="hidden" accept="image/*">
                                        </label>
                                        <div class="mt-2 text-center">
                                            <p class="text-xs font-bold text-[#001B5E]">Profile Picture</p>
                                            <p class="text-[10px] text-gray-500">This will be displayed on your profile</p>
                                        </div>
                                    </div>

                                    {{-- Inputs --}}
                                    <div class="flex-1 grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-semibold text-gray-700">First Name</label>
                                            <input wire:model="firstName" type="text" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            @error('firstName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-700">Last Name</label>
                                            <input wire:model="lastName" type="text" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            @error('lastName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-xs font-semibold text-gray-700">Gender</label>
                                            <select wire:model="gender" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                            @error('gender') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: Contact Information --}}
                            <div>
                                <h3 class="text-base font-bold text-[#070589] flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    Contact Information
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Phone Number</label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">+63</span>
                                            </div>

                                            {{-- UPDATED INPUT: Adds maxlength and JS restriction --}}
                                            <input
                                                wire:model="phoneNumber"
                                                type="text"
                                                maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                class="w-full pl-12 border-gray-100 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Enter 10 digit number"
                                            >
                                        </div>
                                        @error('phoneNumber') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Email Address</label>
                                        <input wire:model="email" type="email" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="text-[10px] text-gray-500 mt-1">Login credentials will be sent to this email</p>
                                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: Rent Details --}}
                            <div>
                                <h3 class="text-base font-bold text-[#070589] flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    Rent Details
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Building Name</label>
                                        <select wire:model.live="selectedBuilding" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Building</option>
                                            @foreach($buildings as $b)
                                                <option value="{{ $b->property_id }}">{{ $b->building_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Unit Number</label>
                                        <select wire:model.live="selectedUnit" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" {{ empty($units) ? 'disabled' : '' }}>
                                            <option value="">Select Unit</option>
                                            @foreach($units as $u)
                                                <option value="{{ $u->unit_id }}">{{ $u->unit_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Bed Number</label>
                                        <select wire:model.live="selectedBed" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" {{ empty($beds) ? 'disabled' : '' }}>
                                            <option value="">Select Bed</option>
                                            @foreach($beds as $bed)
                                                <option value="{{ $bed->bed_id }}">{{ $bed->bed_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Dorm Type</label>
                                        <select wire:model="dormType" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Type</option>
                                            <option value="All Female">All Female</option>
                                            <option value="All Male">All Male</option>
                                            <option value="Co-ed">Co-ed</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Term</label>
                                        <select wire:model="term" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Term</option>
                                            <option value="6 Months">6 Months</option>
                                            <option value="12 Months">1 Year</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Shift</label>
                                        <select wire:model="shift" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Shift</option>
                                            <option value="Day">Day</option>
                                            <option value="Night">Night</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-xs font-semibold text-gray-700">Start Date</label>
                                        <input wire:model="startDate" type="date" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div class="col-span-2 flex items-center gap-2">
                                        <input wire:model="autoRenew" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label class="text-xs font-semibold text-gray-700">Auto Renew Contract</label>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 4: Move In Details --}}
                            <div>
                                <h3 class="text-base font-bold text-[#070589] flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Move In Details
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Move In Date</label>
                                        <input wire:model="moveInDate" type="date" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Monthly Rate</label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 text-xs">₱</span></div>
                                            <input wire:model="monthlyRate" type="number" class="w-full pl-8 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Security Deposit</label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 text-xs">₱</span></div>
                                            <input wire:model="securityDeposit" type="number" class="w-full pl-8 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700">Payment Status</label>
                                        <select wire:model="paymentStatus" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Status</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-xs font-semibold text-gray-700">Registration</label>
                                        <input wire:model="registration" type="text" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="---">
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 bg-white border-t border-gray-200 flex justify-end">
                    {{-- Save Button (Triggers Confirm Modal) --}}
                    <button
                        type="button"
                        x-on:click="$dispatch('open-modal', 'save-tenant-confirmation')"
                        class="bg-[#070589] hover:bg-[#000060] text-white font-bold py-3 px-10 rounded-xl text-sm transition-colors shadow-lg"
                    >
                        Save Tenant
                    </button>
                </div>

            </div>
        </div>

        {{-- REUSABLE COMPONENTS --}}

        {{-- 1. Confirmation Modal (Save) --}}
        <x-ui.modal-confirm
            name="save-tenant-confirmation"
            title="Save Tenant?"
            description="Are you sure you want to save this tenant's information?"
            confirmText="Yes, Save"
            cancelText="Cancel"
            confirmAction="save"
        />

        {{-- 2. Cancel Modal (Discard) --}}
        <x-ui.modal-cancel
            name="discard-tenant-confirmation"
            title="Discard Unsaved Changes?"
            description="Are you sure you want to close? All details will be lost."
            discardText="Discard"
            returnText="Keep Editing"
            discardAction="close"
        />

    @endif
</div>
