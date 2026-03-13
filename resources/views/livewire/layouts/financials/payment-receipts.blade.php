<div class="font-sans">
    <x-ui.card-with-tabs
         :tabs="['all' => 'All', 'upcoming' => 'Upcoming', 'paid' => 'Paid', 'unpaid' => 'Unpaid']"
         :counts="$counts"
         :activeTab="$activeTab"
         wire:model.live="activeTab"
    >
        {{-- FILTERS SLOT --}}
        <x-slot:filters>
        </x-slot:filters>

        {{-- TABLE SLOT --}}
        <x-ui.table>
            <x-slot:head>
                <x-ui.th>Tenant Name</x-ui.th>
                <x-ui.th>Billing Date</x-ui.th>
                <x-ui.th>Amount</x-ui.th>
                <x-ui.th>Status</x-ui.th>
                <x-ui.th class="text-center">Action</x-ui.th>
            </x-slot:head>

            <x-slot:body>
                @forelse ($payments as $payment)
                    <x-ui.tr wire:key="payment-{{ $payment->billing_id }}"
                            wire:click="viewReceipt({{ $payment->billing_id }})"
                            class="cursor-pointer hover:bg-gray-50 transition-colors group">

                        <x-ui.td class="group-hover:text-blue-600 font-medium">
                            {{ $payment->first_name }} {{ $payment->last_name }}
                        </x-ui.td>

                        <x-ui.td>
                            {{ \Carbon\Carbon::parse($payment->billing_date)->format('M d, Y') }}
                        </x-ui.td>

                        <x-ui.td>
                            ₱ {{ number_format($payment->to_pay, 2) }}
                        </x-ui.td>

                        <x-ui.td>
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $payment->status === 'Paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $payment->status }}
                            </span>
                        </x-ui.td>

                        <x-ui.td class="text-center" @click.stop>
                            <div class="inline-flex items-center gap-2">
                                <button
                                    wire:click.stop="viewReceipt({{ $payment->billing_id }})"
                                    class="inline-flex items-center px-3 py-1 border border-[#0906ae] text-[#0906ae] rounded-md text-xs font-bold hover:bg-blue-50 transition-colors"
                                >
                                    View
                                </button>

                                @if($payment->status !== 'Paid')
                                    <button
                                        wire:click.stop="confirmPayment({{ $payment->billing_id }})"
                                        class="inline-flex items-center px-3 py-1 bg-[#070589] text-white rounded-md text-xs font-bold hover:bg-[#000060] transition-colors"
                                    >
                                        Mark As Paid
                                    </button>
                                @endif
                            </div>
                        </x-ui.td>
                    </x-ui.tr>
                @empty
                    <x-ui.tr>
                        <x-ui.td colspan="5" class="text-center py-12 text-slate-500">
                            No payment records found.
                        </x-ui.td>
                    </x-ui.tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <x-slot:footer>
            {{ $payments->links('livewire.layouts.components.paginate-blue') }}
        </x-slot:footer>
    </x-ui.card-with-tabs>

   <livewire:layouts.financials.payment-receipt-modal />

    <x-ui.modal-confirm
        name="mark-as-paid-confirmation"
        title="Confirm Payment"
        description="Are you sure you want to mark this transaction as PAID?"
        confirmText="Yes, Confirm"
        cancelText="Cancel"
        confirmAction="markAsPaid"
    />
</div>
