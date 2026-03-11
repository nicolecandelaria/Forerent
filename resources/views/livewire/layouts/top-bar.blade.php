<div class="flex items-center justify-end gap-4 px-6 py-4">


    @unless(
        request()->is('tenant') ||
        request()->is('landlord') ||
        request()->is('manager') ||
        request()->is('admin') ||
        request()->is('*dashboard*')
    )
        <div class="relative flex-1 max-w-xl">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <input
                type="text"
                wire:model.live.debounce.300ms="searchQuery"
                autocomplete="off"
                class="w-full pl-12 pr-10 py-3 text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm"
                placeholder="Search properties, units, tenants...">

            @if($searchQuery)
            <button
                wire:click="clearSearch"
                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            @endif
        </div>
    @endunless
  
    <a href="{{ url('settings') }}" wire:navigate class="flex items-center justify-center w-12 h-12 bg-white border-2 border-blue-600 rounded-full hover:bg-blue-50 transition-all duration-200 shadow-sm cursor-pointer">
        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
        </svg>
    </a>

</div>
