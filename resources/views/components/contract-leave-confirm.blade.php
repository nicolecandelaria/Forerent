{{-- Contract Leave Confirmation Modal --}}
{{-- Usage: wrap the contract modal's outer div with x-data="{ showLeaveConfirm: false }" --}}
{{-- Then call showLeaveConfirm = true instead of directly closing --}}
<div x-show="showLeaveConfirm" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40"
     @click.self="showLeaveConfirm = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 mx-4" @click.stop>
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Leave without signing?</h3>
                <p class="text-sm text-gray-500 mt-1">This contract has not been signed yet. Are you sure you want to close it?</p>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button @click="showLeaveConfirm = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                Stay
            </button>
            <button @click="showLeaveConfirm = false; {{ $closeAction ?? '' }}" class="px-4 py-2 text-sm font-semibold text-white bg-[#070589] hover:bg-[#000060] rounded-xl transition-colors">
                Leave
            </button>
        </div>
    </div>
</div>
