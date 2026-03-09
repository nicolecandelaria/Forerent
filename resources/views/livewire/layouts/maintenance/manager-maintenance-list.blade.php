<div class="h-full">
    <x-ui.list-view
        title="Maintenance History"
        :tabs="['all' => 'All', 'pending' => 'Pending', 'ongoing' => 'Ongoing', 'completed' => 'Completed']"
        :activeTab="$activeTab"
        :counts="$counts"
    >
        @forelse($requests as $req)
            @php
                $isActive = $activeRequestId === $req->request_id;

                $statusColor = match($req->status) {
                    'Ongoing'   => 'bg-yellow-400',
                    'Pending'   => 'bg-orange-400',
                    'Completed' => 'bg-green-400',
                    default     => 'bg-gray-300'
                };

                $ticketId = $req->ticket_number
                    ?? 'TKT-' . str_pad($req->request_id, 4, '0', STR_PAD_LEFT);
            @endphp

            <div
                wire:click="selectRequest({{ $req->request_id }})"
                class="group cursor-pointer rounded-xl p-4 border transition-all duration-200 relative overflow-hidden
                    {{ $isActive
                        ? 'bg-[#2B66F5] border-[#2B66F5] shadow-md'
                        : 'bg-white border-gray-100 hover:border-blue-300 hover:shadow-sm' }}"
            >
                <div class="relative z-10 flex justify-between items-start">
                    <div class="min-w-0 flex-1">
                        {{-- Tenant Name --}}
                        <p class="text-xs font-bold uppercase tracking-wide mb-0.5 truncate
                            {{ $isActive ? 'text-blue-200' : 'text-gray-400' }}">
                            {{ $req->tenant_name ?? 'Unknown Tenant' }}
                        </p>
                        {{-- Unit --}}
                        <h3 class="text-base font-extrabold
                            {{ $isActive ? 'text-white' : 'text-[#070642] group-hover:text-[#2B66F5]' }}">
                            Unit {{ $req->unit_number }}
                        </h3>
                        {{-- Ticket + Category --}}
                        <p class="text-[10px] mt-0.5 {{ $isActive ? 'text-blue-200' : 'text-gray-400' }}">
                            {{ $ticketId }} · {{ $req->category ?? '—' }}
                        </p>
                    </div>
                    {{-- Status dot --}}
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-1 {{ $statusColor }}
                        {{ $isActive ? 'ring-2 ring-white/30' : '' }}"></span>
                </div>

                @if($isActive)
                    <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="white">
                            <path d="M12 2L2 7l10 5 10-5-10-5zm0 13l-7-3.5V15l7 3.5 7-3.5v-3.5L12 15z"/>
                        </svg>
                    </div>
                @endif
            </div>

        @empty
            <div class="flex flex-col items-center justify-center h-full py-10 text-gray-400">
                <div class="bg-[#F4F7FF] p-6 rounded-full mb-3">
                    <svg class="w-10 h-10 text-[#2B66F5] opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">No tickets found</p>
            </div>
        @endforelse

    </x-ui.list-view>
</div>
