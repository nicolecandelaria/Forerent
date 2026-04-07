<div class="space-y-4">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div wire:click="setFilter('all')" class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 cursor-pointer transition hover:shadow-md {{ $filter === 'all' ? 'ring-2 ring-blue-500' : '' }}">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">{{ $totalContracts }}</p>
                <p class="text-xs text-gray-500">Total Contracts</p>
            </div>
        </div>

        <div wire:click="setFilter('pending')" class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 cursor-pointer transition hover:shadow-md {{ $filter === 'pending' ? 'ring-2 ring-amber-500' : '' }}">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">{{ $pendingContracts }}</p>
                <p class="text-xs text-gray-500">Pending Signature</p>
            </div>
        </div>

        <div wire:click="setFilter('signed')" class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 cursor-pointer transition hover:shadow-md {{ $filter === 'signed' ? 'ring-2 ring-emerald-500' : '' }}">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">{{ $signedContracts }}</p>
                <p class="text-xs text-gray-500">Fully Signed</p>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by tenant name, property, or unit..."
            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white"
        />
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[20px] md:rounded-[30px] shadow-sm p-4 md:p-6">
        <x-ui.table>
            <x-slot:head>
                <x-ui.th>Tenant</x-ui.th>
                <x-ui.th>Property / Unit</x-ui.th>
                <x-ui.th>Lease Period</x-ui.th>
                <x-ui.th>Lease</x-ui.th>
                <x-ui.th>Signatures</x-ui.th>
                <x-ui.th>Contract</x-ui.th>
                <x-ui.th>Action</x-ui.th>
            </x-slot:head>

            <x-slot:body>
                @forelse($leases as $lease)
                    @php
                        $cStatus = $lease->contract_status ?? 'draft';
                        $cBadge = match($cStatus) {
                            'executed' => 'bg-emerald-100 text-emerald-700',
                            'pending_signatures', 'pending_tenant', 'pending_owner', 'pending_manager' => 'bg-amber-100 text-amber-700',
                            default => 'bg-gray-100 text-gray-500',
                        };
                        $cLabel = match($cStatus) {
                            'executed' => 'Signed',
                            'pending_signatures' => 'Pending',
                            'pending_tenant' => 'Pending Tenant',
                            'pending_owner' => 'Pending Owner',
                            'pending_manager' => 'Pending Manager',
                            'draft' => 'Draft',
                            default => ucfirst(str_replace('_', ' ', $cStatus)),
                        };
                        $sigCount = collect([$lease->owner_signature, $lease->manager_signature, $lease->tenant_signature])->filter()->count();
                    @endphp
                    <x-ui.tr>
                        <x-ui.td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                    {{ strtoupper(substr($lease->tenant?->first_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">{{ $lease->tenant?->first_name }} {{ $lease->tenant?->last_name }}</p>
                                    <p class="text-[11px] text-gray-400">Bed #{{ $lease->bed?->bed_number }}</p>
                                </div>
                            </div>
                        </x-ui.td>

                        <x-ui.td>
                            <p class="text-sm font-medium text-gray-700">{{ $lease->bed?->unit?->property?->property_name }}</p>
                            <p class="text-[11px] text-gray-400">Unit {{ $lease->bed?->unit?->unit_number }}</p>
                        </x-ui.td>

                        <x-ui.td>
                            <p class="text-sm text-gray-600">{{ $lease->start_date?->format('M d, Y') }}</p>
                            <p class="text-[11px] text-gray-400">to {{ $lease->end_date?->format('M d, Y') }}</p>
                        </x-ui.td>

                        <x-ui.td>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $lease->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $lease->status }}
                            </span>
                        </x-ui.td>

                        <x-ui.td>
                            <div class="flex items-center gap-1">
                                <div title="Owner: {{ $lease->owner_signature ? 'Signed' : 'Not signed' }}" class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $lease->owner_signature ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">O</div>
                                <div title="Manager: {{ $lease->manager_signature ? 'Signed' : 'Not signed' }}" class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $lease->manager_signature ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">M</div>
                                <div title="Tenant: {{ $lease->tenant_signature ? 'Signed' : 'Not signed' }}" class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $lease->tenant_signature ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">T</div>
                                <span class="text-[10px] text-gray-400 ml-0.5">{{ $sigCount }}/3</span>
                            </div>
                        </x-ui.td>

                        <x-ui.td>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cBadge }}">{{ $cLabel }}</span>
                        </x-ui.td>

                        <x-ui.td>
                            <button wire:click="viewContract({{ $lease->lease_id }}, 'move-in')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </button>
                        </x-ui.td>
                    </x-ui.tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm text-gray-400">No contracts found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        @if($leases->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center">
                {{ $leases->links() }}
            </div>
        @endif
    </div>
</div>
