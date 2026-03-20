<div class="min-h-screen font-sans">
    
    {{-- TITLE LABEL --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#070642]">Payment Receipts</h2>
    </div>

    <x-ui.card-with-tabs
        :tabs="[
            'all' => 'All', 
            'upcoming' => 'Upcoming', 
            'paid' => 'Paid', 
            'unpaid' => 'Unpaid' 
        ]"
        :activeTab="$activeTab"
    >
        {{-- DROPDOWNS (In the Right Slot) --}}
        <x-slot:filters>
            {{-- Month Filter --}}
            <x-dropdown label="{{ $monthOptions[$selectedMonth] ?? 'Month' }}">
                <x-dropdown-item wire:click="$set('selectedMonth', null)" @click="open = false">
                    All Months
                </x-dropdown-item>
                @foreach ($monthOptions as $value => $label)
                    <x-dropdown-item 
                        wire:click="$set('selectedMonth', {{ $value }})" 
                        @click="open = false" 
                        :active="$selectedMonth == $value"
                    >
                        {{ $label }}
                    </x-dropdown-item>
                @endforeach
            </x-dropdown>

            {{-- Building Filter --}}
            <x-dropdown label="{{ $selectedBuilding ?? 'Building' }}">
                <x-dropdown-item wire:click="$set('selectedBuilding', null)" @click="open = false">
                    All Buildings
                </x-dropdown-item>
                @foreach ($buildingOptions as $value => $label)
                    <x-dropdown-item 
                        wire:click="$set('selectedBuilding', '{{ $value }}')" 
                        @click="open = false"
                        :active="$selectedBuilding === $value"
                    >
                        {{ $label }}
                    </x-dropdown-item>
                @endforeach
            </x-dropdown>
        </x-slot:filters>

        {{-- TABLE --}}
        <x-ui.table>
            <x-slot:head>
                <x-ui.th>Unit Number</x-ui.th>
                <x-ui.th>Tenant Name</x-ui.th>
                <x-ui.th>Due Date</x-ui.th>
                <x-ui.th>Total Amount</x-ui.th>
                <x-ui.th>Payment Status</x-ui.th>
            </x-slot:head>

            <x-slot:body>
                @forelse ($payments as $payment)
                    <x-ui.tr>
                        <x-ui.td isHeader="true">{{ $payment->reference_number }}</x-ui.td>
                        <x-ui.td>{{ Auth::user()->name ?? 'Tenant Name' }}</x-ui.td>
                        <x-ui.td>{{ \Carbon\Carbon::parse($payment->transaction_date)->format('F d, Y') }}</x-ui.td>
                        <x-ui.td class="font-bold text-[#070642]">₱ {{ number_format($payment->amount, 2) }}</x-ui.td>
                        <x-ui.td>
                            @php
                                $status = $payment->transaction_type === 'Credit' ? 'Paid' : 'Unpaid';
                                $badgeClass = match($status) {
                                    'Paid' => 'bg-blue-100 text-blue-600',
                                    'Unpaid' => 'bg-red-100 text-red-600',
                                    'Upcoming' => 'bg-orange-100 text-orange-600',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </x-ui.td>
                    </x-ui.tr>
                @empty
                    <tr><td colspan="5" class="text-center py-8 text-gray-500">No records found.</td></tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <x-slot:footer>
            {{ $payments->onEachSide(1)->links('livewire.layouts.components.paginate-blue') }}
        </x-slot:footer>
    </x-ui.card-with-tabs>
</div>
