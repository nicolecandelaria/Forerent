<div class="font-sans">

    <div class="font-sans">
    <x-ui.card-with-tabs
         :tabs="[
            'all'      => 'All',
            'upcoming' => 'Upcoming',
            'paid'     => 'Paid',
            'unpaid'   => 'Unpaid'
        ]"

         :counts="$counts"

        :activeTab="$activeTab"
        wire:model.live="activeTab"
    >

        {{-- FILTERS SLOT --}}
        <x-slot:filters>
            <div class="flex flex-col sm:flex-row items-center gap-3">

                {{-- Month Dropdown --}}
                @php
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    // Calculate Label: If filter is selected, show that month name, else 'Month'
                    $currentLabel = $filterPeriod ? $months[$filterPeriod] : 'Month';
                @endphp

                <x-dropdown
                    label="{{ $currentLabel }}"
                    align="right"
                    width="w-48"
                >
                    {{-- Option 1: All Time --}}
                    <x-dropdown-item
                        wire:click="setFilterPeriod('')"
                        @click="open = false"
                        :active="$filterPeriod === ''"
                    >
                        All Months
                    </x-dropdown-item>

                    {{-- Option 2: Loop 12 Months --}}
                    @foreach($months as $num => $name)
                        <x-dropdown-item
                            wire:click="setFilterPeriod({{ $num }})"
                            @click="open = false"
                            :active="$filterPeriod == $num"
                        >
                            {{ $name }}
                        </x-dropdown-item>
                    @endforeach
                </x-dropdown>

                {{-- Building Dropdown (Unchanged) --}}
                <x-dropdown
                    label="{{ $filterBuilding ? 'Building ' . $filterBuilding : 'Building 1' }}"
                    align="right"
                    width="w-48"
                >
                    <x-dropdown-item wire:click="$set('filterBuilding', '')" @click="open = false" :active="$filterBuilding === ''">
                        All Buildings
                    </x-dropdown-item>
                    <x-dropdown-item wire:click="$set('filterBuilding', '1')" @click="open = false" :active="$filterBuilding === '1'">
                        Building 1
                    </x-dropdown-item>
                </x-dropdown>

            </div>
        </x-slot:filters>

        {{-- 2. TABLE CONTENT --}}
        <x-ui.table>
            <x-slot:head>
                <x-ui.th>Transaction ID</x-ui.th>
                <x-ui.th>Tenant Name</x-ui.th>
                <x-ui.th>Due Date</x-ui.th>
                <x-ui.th>Period Covered</x-ui.th>
                <x-ui.th>Total Amount</x-ui.th>
                <x-ui.th class="text-center">Payment Status</x-ui.th>
                <x-ui.th class="text-center">Conform Payment</x-ui.th>
            </x-slot:head>

            <x-slot:body>
                @forelse ($payments as $payment)
                    <x-ui.tr
                        wire:key="payment-{{ $payment->billing_id }}"
                        wire:click="viewReceipt({{ $payment->billing_id }})"
                        class="cursor-pointer hover:bg-blue-50 transition-colors"
                    >
                        {{-- Table cells remain the same... --}}
                        <x-ui.td class="font-bold text-[#1E0E4B]">
                            FT-{{ 202300 + $payment->billing_id }}
                        </x-ui.td>

                        {{-- Tenant Name --}}
                        <x-ui.td class="text-[#1E0E4B] font-medium">
                            {{ $payment->first_name }} {{ $payment->last_name }}
                        </x-ui.td>

                        {{-- Due Date --}}
                        <x-ui.td class="text-[#1E0E4B]">
                            {{ Carbon\Carbon::parse($payment->billing_date)->format('F d, Y') }}
                        </x-ui.td>

                        {{-- Period Covered --}}
                        <x-ui.td class="text-[#1E0E4B]">
                            {{ Carbon\Carbon::parse($payment->billing_date)->format('M') }}-{{ Carbon\Carbon::parse($payment->next_billing)->format('M Y') }}
                        </x-ui.td>

                        {{-- Total Amount --}}
                        <x-ui.td class="font-bold text-[#1E0E4B]">
                            ₱ {{ number_format($payment->to_pay, 0) }}
                        </x-ui.td>

                        {{-- Status Badges --}}
                        <x-ui.td class="text-center">
                            @php
                                $status = strtolower($payment->status);
                            @endphp

                            @if($status === 'paid')
                                <span class="inline-flex items-center justify-center px-6 py-2 rounded-full text-xs font-bold bg-[#D4F4DD] text-[#537D3A] min-w-[100px]">
                                    Paid
                                </span>
                            @elseif($status === 'overdue')
                                <span class="inline-flex items-center justify-center px-6 py-2 rounded-full text-xs font-bold bg-[#FEE2E2] text-[#DC2626] min-w-[100px]">
                                    Overdue
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center px-6 py-2 rounded-full text-xs font-bold bg-[#FFF3DD] text-[#CD8500] min-w-[100px]">
                                    Upcoming
                                </span>
                            @endif
                        </x-ui.td>

                        {{-- Conform Payment Button --}}
                        <x-ui.td class="text-center">
                            @php
                                $status = strtolower($payment->status);
                            @endphp

                            @if($status !== 'paid')
                                {{-- UPDATED: Now calls confirmPayment instead of markAsPaid directly --}}
                                <button
                                    wire:click="confirmPayment({{ $payment->billing_id }})"
                                    class="inline-flex items-center justify-center px-4 py-1 rounded border border-[#22C55E] text-[#22C55E] text-xs font-bold hover:bg-[#22C55E] hover:text-white transition-all min-w-[100px]"
                                >
                                    Mark As Paid
                                </button>
                            @else
                                <button disabled class="inline-flex items-center justify-center px-4 py-1 rounded border border-gray-200 text-gray-400 text-xs font-bold min-w-[100px] cursor-not-allowed bg-white">
                                    Paid
                                </button>
                            @endif
                        </x-ui.td>
                    </x-ui.tr>
                @empty
                    <x-ui.tr>
                        <x-ui.td colspan="7" class="text-center py-12 text-slate-500">
                            No payment records found.
                        </x-ui.td>
                    </x-ui.tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        {{-- 3. PAGINATION FOOTER (Using your custom blue template) --}}
        <x-slot:footer>
            {{ $payments->links('livewire.layouts.components.paginate-blue') }}
        </x-slot:footer>

    </x-ui.card-with-tabs>

    {{-- ADDED: Confirmation Modal --}}
    <x-ui.modal-confirm
        name="mark-as-paid-confirmation"
        title="Confirm Payment"
        description="Are you sure you want to mark this transaction as PAID? This action will update the status immediately."
        confirmText="Yes, Confirm"
        cancelText="Cancel"
        confirmAction="markAsPaid"
    />

</div>
