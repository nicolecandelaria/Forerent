<div class="w-full space-y-6">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 cursor-pointer transition hover:shadow-md {{ $filter === 'all' ? 'ring-2 ring-blue-500' : '' }}"
             wire:click="$set('filter', 'all')">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalContracts }}</p>
                <p class="text-xs text-gray-500">Total Contracts</p>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 cursor-pointer transition hover:shadow-md {{ $filter === 'pending' ? 'ring-2 ring-amber-500' : '' }}"
             wire:click="$set('filter', 'pending')">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingContracts }}</p>
                <p class="text-xs text-gray-500">Pending Signature</p>
            </div>
        </div>

        {{-- Signed --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 cursor-pointer transition hover:shadow-md {{ $filter === 'signed' ? 'ring-2 ring-emerald-500' : '' }}"
             wire:click="$set('filter', 'signed')">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $signedContracts }}</p>
                <p class="text-xs text-gray-500">Fully Signed</p>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by tenant name, property, or unit..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
            />
        </div>
    </div>

    {{-- Contracts Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Tenant</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Property / Unit</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Lease Period</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Lease</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Signatures</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Contract</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
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
                                'pending_signatures' => 'Pending Signatures',
                                'pending_tenant' => 'Pending Tenant',
                                'pending_owner' => 'Pending Owner',
                                'pending_manager' => 'Pending Manager',
                                'draft' => 'Draft',
                                default => ucfirst(str_replace('_', ' ', $cStatus)),
                            };

                            $sigCount = collect([
                                $lease->owner_signature,
                                $lease->manager_signature,
                                $lease->tenant_signature,
                            ])->filter()->count();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Tenant --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700">
                                        {{ strtoupper(substr($lease->tenant?->first_name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $lease->tenant?->first_name }} {{ $lease->tenant?->last_name }}</p>
                                        <p class="text-xs text-gray-400">Bed #{{ $lease->bed?->bed_number }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Property / Unit --}}
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-700">{{ $lease->bed?->unit?->property?->property_name }}</p>
                                <p class="text-xs text-gray-400">Unit {{ $lease->bed?->unit?->unit_number }}</p>
                            </td>

                            {{-- Lease Period --}}
                            <td class="px-5 py-4 text-gray-600">
                                <p>{{ $lease->start_date?->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">to {{ $lease->end_date?->format('M d, Y') }}</p>
                            </td>

                            {{-- Lease Status --}}
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $lease->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $lease->status }}
                                </span>
                            </td>

                            {{-- Signatures --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1">
                                    {{-- Owner --}}
                                    <div title="Owner: {{ $lease->owner_signature ? 'Signed' : 'Not signed' }}" class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $lease->owner_signature ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        O
                                    </div>
                                    {{-- Manager --}}
                                    <div title="Manager: {{ $lease->manager_signature ? 'Signed' : 'Not signed' }}" class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $lease->manager_signature ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        M
                                    </div>
                                    {{-- Tenant --}}
                                    <div title="Tenant: {{ $lease->tenant_signature ? 'Signed' : 'Not signed' }}" class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $lease->tenant_signature ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        T
                                    </div>
                                    <span class="text-[11px] text-gray-400 ml-1">{{ $sigCount }}/3</span>
                                </div>
                            </td>

                            {{-- Contract Status --}}
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $cBadge }}">
                                    {{ $cLabel }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <button
                                    wire:click="viewContract({{ $lease->lease_id }}, 'move-in')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </button>
                            </td>
                        </tr>
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
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($leases->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $leases->links() }}
            </div>
        @endif
    </div>

    {{-- Landlord Contract Viewer Modal (reuse existing) --}}
    <livewire:layouts.units.landlord-contract-viewer />
</div>
