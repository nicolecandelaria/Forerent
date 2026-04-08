@php
    $authUser = auth()->user();
    $fallbackFirstName = $firstName !== '' ? $firstName : (string) ($authUser->first_name ?? '');
    $fallbackLastName = $lastName !== '' ? $lastName : (string) ($authUser->last_name ?? '');
    $fallbackEmail = $email !== '' ? $email : (string) ($authUser->email ?? '');
    $fallbackPhoneRaw = preg_replace('/\D/', '', (string) ($authUser->contact ?? '')) ?? '';
    $fallbackPhone = $phoneNumber !== '' ? $phoneNumber : ($fallbackPhoneRaw !== '' ? substr($fallbackPhoneRaw, -10) : '');
@endphp

<div class="w-full" style="font-family: 'Open Sans', sans-serif;">
    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 9999px white inset !important;
            box-shadow: 0 0 0 9999px white inset !important;
            -webkit-text-fill-color: #374151 !important;
            transition: background-color 9999s ease-in-out 0s;
        }
    </style>
    <form
        class="w-full bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.08)] p-6 md:p-8"
        wire:submit.prevent="save"
        x-data="{ editing: false }"
    >

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('settings'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $errors->first('settings') }}
            </div>
        @endif

        {{-- Profile Picture + Edit Toggle --}}
        <div class="mb-8 flex items-start gap-6">
            <div class="shrink-0"
                 x-data="{ pfpUploading: false, pfpProgress: 0, pfpError: false }"
                 x-on:livewire-upload-start="pfpUploading = true; pfpProgress = 0; pfpError = false"
                 x-on:livewire-upload-finish="pfpUploading = false; pfpProgress = 100"
                 x-on:livewire-upload-cancel="pfpUploading = false"
                 x-on:livewire-upload-error="pfpUploading = false; pfpError = true"
                 x-on:livewire-upload-progress="pfpProgress = $event.detail.progress"
            >
                <label class="cursor-pointer group relative block w-24 h-24">
                    @if ($profilePicture)
                        <img src="{{ $profilePicture->temporaryUrl() }}" class="w-full h-full rounded-full object-cover border-4 border-[#001B5E]">
                    @elseif ($this->existingProfileImgUrl)
                        <img src="{{ $this->existingProfileImgUrl }}" class="w-full h-full rounded-full object-cover border-4 border-[#001B5E]">
                    @else
                        <div class="w-full h-full rounded-full bg-[#001B5E] flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </div>
                    @endif
                    {{-- Progress overlay with percentage --}}
                    <div x-show="pfpUploading" x-cloak class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50">
                        <span class="text-white text-xs font-bold" x-text="pfpProgress + '%'"></span>
                    </div>
                    {{-- Camera icon (bottom-right) --}}
                    <div x-show="editing" x-cloak class="absolute bottom-0 right-0 bg-white rounded-full p-1 border shadow">
                        <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <input x-show="editing" type="file" wire:model="profilePicture" name="profilePicture" class="hidden" accept="image/*">
                </label>
                {{-- Error message --}}
                <div x-show="pfpError" x-cloak class="mt-2 w-24">
                    <p class="text-[11px] text-red-500 font-medium text-center">Upload failed!</p>
                </div>
                {{-- Progress bar below avatar --}}
                <div x-show="pfpUploading" x-cloak class="mt-2 w-24">
                    <div class="h-1 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-[#2360E8] rounded-full transition-all duration-200" :style="'width: ' + pfpProgress + '%'"></div>
                    </div>
                    <p class="text-[11px] text-[#2360E8] mt-0.5 font-medium text-center">Uploading...</p>
                </div>
            </div>
            <div class="flex-1 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold leading-tight text-[#0C0B50]">Profile Picture</h3>
                        <p class="mt-1 text-sm text-gray-500">This will be displayed on your profile</p>
                    </div>
                    {{-- Edit / Close toggle button --}}
                    <button type="button" @click="editing = !editing" class="flex h-9 w-9 items-center justify-center rounded-full text-[#2360E8] transition-colors duration-200 hover:bg-blue-100" :class="editing ? 'bg-red-50 !text-red-500 hover:!bg-red-100' : ''">
                        {{-- Edit icon --}}
                        <svg x-show="!editing" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        {{-- X icon --}}
                        <svg x-show="editing" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                @error('profilePicture') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- First Name / Last Name --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="first_name" class="mb-1.5 block text-sm font-semibold text-gray-700">First Name</label>
                <div x-show="!editing" class="flex h-12 items-center rounded-xl border border-gray-200 bg-gray-50 px-4 shadow-sm">
                    <span class="w-full text-sm text-gray-700">{{ $firstName ?: 'Not set' }}</span>
                </div>
                <div x-show="editing" x-cloak class="flex h-12 items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm transition-all duration-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                    <input
                        type="text"
                        id="first_name"
                        wire:model.live="firstName"
                        placeholder="Enter first name"
                        class="w-full border-0 bg-transparent text-sm text-gray-700 outline-none ring-0 placeholder:text-gray-300 focus:border-0 focus:outline-none focus:ring-0"
                    >
                </div>
                @error('firstName') <p class="ml-1 mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="last_name" class="mb-1.5 block text-sm font-semibold text-gray-700">Last Name</label>
                <div x-show="!editing" class="flex h-12 items-center rounded-xl border border-gray-200 bg-gray-50 px-4 shadow-sm">
                    <span class="w-full text-sm text-gray-700">{{ $lastName ?: 'Not set' }}</span>
                </div>
                <div x-show="editing" x-cloak class="flex h-12 items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm transition-all duration-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                    <input
                        type="text"
                        id="last_name"
                        wire:model.live="lastName"
                        placeholder="Enter last name"
                        class="w-full border-0 bg-transparent text-sm text-gray-700 outline-none ring-0 placeholder:text-gray-300 focus:border-0 focus:outline-none focus:ring-0"
                    >
                </div>
                @error('lastName') <p class="ml-1 mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="mb-4 mt-8 flex items-center gap-3">
            <h4 class="text-xl font-bold leading-tight text-[#0C0B50] md:text-xl">Contact Information</h4>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="phone_number" class="mb-1.5 block text-sm font-semibold text-gray-700">Phone Number</label>
                <div x-show="!editing" class="flex h-12 items-center rounded-xl border border-gray-200 bg-gray-50 shadow-sm">
                    <span class="inline-flex h-full items-center whitespace-nowrap border-r border-gray-200 px-3 text-xs font-semibold uppercase tracking-wide text-gray-400">PH +63 9</span>
                    <span class="w-full px-3 text-sm text-gray-700">{{ $phoneNumber ?: 'Not set' }}</span>
                </div>
                <div x-show="editing" x-cloak class="flex h-12 items-center rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                    <span class="inline-flex h-full items-center whitespace-nowrap border-r border-gray-200 px-3 text-xs font-semibold uppercase tracking-wide text-gray-400">PH +63 9</span>
                    <input
                        type="tel"
                        id="phone_number"
                        wire:model.live="phoneNumber"
                        inputmode="numeric"
                        maxlength="9"
                        pattern="[0-9]{9}"
                        oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)"
                        placeholder="Enter phone number"
                        class="w-full border-0 bg-transparent px-3 text-sm text-gray-700 outline-none ring-0 placeholder:text-gray-300 focus:border-0 focus:outline-none focus:ring-0"
                    >
                </div>
                @error('phoneNumber') <p class="ml-1 mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email Address</label>
                <div x-show="!editing" class="flex h-12 items-center rounded-xl border border-gray-200 bg-gray-50 px-4 shadow-sm">
                    <span class="w-full text-sm text-gray-700">{{ $email ?: 'Not set' }}</span>
                </div>
                <div x-show="editing" x-cloak class="flex h-12 items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm transition-all duration-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                    <input
                        type="email"
                        id="email"
                        wire:model.live="email"
                        placeholder="Enter email address"
                        class="w-full border-0 bg-transparent text-sm text-gray-700 outline-none ring-0 placeholder:text-gray-300 focus:border-0 focus:outline-none focus:ring-0"
                    >
                </div>
                @error('email') <p class="ml-1 mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Valid ID Section --}}
        <div class="mb-4 mt-8 flex items-center gap-3">
            <h4 class="text-xl font-bold leading-tight text-[#0C0B50] md:text-xl">Valid ID</h4>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-6">
            <div>
                <label for="government_id_type" class="mb-1.5 block text-sm font-semibold text-gray-700">ID Type</label>
                <div x-show="!editing" class="flex h-12 items-center rounded-xl border border-gray-200 bg-gray-50 px-4 shadow-sm">
                    <span class="w-full text-sm text-gray-700">{{ $governmentIdType ?: 'Not set' }}</span>
                </div>
                <div x-show="editing" x-cloak class="flex h-12 items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm transition-all duration-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                    <select
                        id="government_id_type"
                        wire:model.live="governmentIdType"
                        class="w-full border-0 bg-transparent text-sm text-gray-700 outline-none ring-0 focus:border-0 focus:outline-none focus:ring-0"
                    >
                        <option value="">Select ID Type</option>
                        <option value="Passport">Passport</option>
                        <option value="Driver's License">Driver's License</option>
                        <option value="UMID">UMID</option>
                        <option value="National ID">National ID</option>
                        <option value="Postal ID">Postal ID</option>
                    </select>
                </div>
                @error('governmentIdType') <p class="ml-1 mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="government_id_number" class="mb-1.5 block text-sm font-semibold text-gray-700">ID Number</label>
                <div x-show="!editing" class="flex h-12 items-center rounded-xl border border-gray-200 bg-gray-50 px-4 shadow-sm">
                    <span class="w-full text-sm text-gray-700">{{ $governmentIdNumber ?: 'Not set' }}</span>
                </div>
                <div x-show="editing" x-cloak class="flex h-12 items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm transition-all duration-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                    <input
                        type="text"
                        id="government_id_number"
                        wire:model.live="governmentIdNumber"
                        placeholder="Enter ID number"
                        class="w-full border-0 bg-transparent text-sm text-gray-700 outline-none ring-0 placeholder:text-gray-300 focus:border-0 focus:outline-none focus:ring-0"
                    >
                </div>
                @error('governmentIdNumber') <p class="ml-1 mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ID Photo display --}}
        @if ($governmentIdImage)
            <div class="relative inline-block w-full max-w-md">
                <img src="{{ $governmentIdImage->temporaryUrl() }}" class="w-full max-h-48 object-contain rounded-xl border border-gray-200 shadow-sm">
                <button type="button" wire:click="removeGovernmentIdImage" class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full border-2 border-white bg-red-500 text-xs font-bold text-white shadow-md hover:bg-red-600">&times;</button>
            </div>
        @elseif ($this->existingGovernmentIdImageUrl)
            <div class="relative inline-block w-full max-w-md">
                <img src="{{ $this->existingGovernmentIdImageUrl }}" class="w-full max-h-48 object-contain rounded-xl border border-gray-200 shadow-sm">
                <button x-show="editing" x-cloak type="button" wire:click="removeGovernmentIdImage" class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full border-2 border-white bg-red-500 text-xs font-bold text-white shadow-md hover:bg-red-600">&times;</button>
            </div>
        @endif

        {{-- ID Photo file input (edit mode) --}}
        <div x-show="editing" x-cloak class="mt-3">
            <label class="mb-1.5 block text-sm font-semibold text-gray-700">ID Photo</label>
            <input
                type="file"
                wire:model="governmentIdImage"
                accept="image/*"
                class="block w-full max-w-md text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-[#2360E8] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#070589] file:cursor-pointer"
            >
            <div wire:loading wire:target="governmentIdImage" class="mt-2 flex items-center gap-2 text-sm text-[#2360E8]">
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Uploading...
            </div>
        </div>

        @error('governmentIdImage') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

        <div x-show="editing" x-cloak class="mt-8 flex items-center justify-end">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save,profilePicture,governmentIdImage"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Update Changes
            </button>
        </div>
    </form>
</div>
