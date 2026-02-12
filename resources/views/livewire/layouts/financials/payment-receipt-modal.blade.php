<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="relative w-full max-w-[850px] bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
             @click.away="close">

            <div class="relative bg-[#1E40AF] overflow-hidden shrink-0">
                <div class="absolute inset-0 bg-gradient-to-r from-[#1E40AF] to-[#3B82F6]"></div>

                <div class="absolute top-0 right-0 h-full w-[60%] bg-white/10"
                     style="border-bottom-left-radius: 100% 100%;">
                </div>
                <div class="absolute top-0 right-0 h-full w-[40%] bg-gradient-to-b from-white/10 to-transparent"
                     style="border-bottom-left-radius: 100% 100%;">
                </div>

                <div class="relative z-10 px-8 py-8 flex justify-between items-start text-white">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight mb-4">PAYMENT RECEIPT</h1>
                        <div class="text-[11px] font-light space-y-1 opacity-90 leading-tight">
                            <p class="flex gap-2"><span class="font-medium w-24">Invoice Number</span> {{ $data['invoice_no'] }}</p>
                            <p class="flex gap-2"><span class="font-medium w-24">Issued Date</span> {{ $data['issued_date'] }}</p>
                            <p class="flex gap-2"><span class="font-medium w-24">Due Date</span> {{ $data['due_date'] }}</p>
                        </div>
                    </div>

                    <button wire:click="close" class="text-white/80 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <div class="space-y-6">
                        <div class="border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-[#1E40AF] text-[11px] font-bold uppercase tracking-wider mb-5">Tenant Information</h3>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Name</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['tenant']['name'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Unit</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['tenant']['unit'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Building</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['tenant']['building'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-start group">
                                    <span class="text-gray-400 font-medium shrink-0 mt-0.5">Address</span>
                                    <span class="font-bold text-[#0F172A] text-right max-w-[60%]">{{ $data['tenant']['address'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Contact</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['tenant']['contact'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-[#1E40AF] text-[11px] font-bold uppercase tracking-wider mb-5">Recipient Information</h3>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Name</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['recipient']['name'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Position</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['recipient']['position'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Contact</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['recipient']['contact'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="border border-gray-200 rounded-2xl p-6 h-fit">
                            <h3 class="text-[#1E40AF] text-[11px] font-bold uppercase tracking-wider mb-5">Payment Details</h3>

                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Date Paid</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['payment']['date_paid'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Transaction ID</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['payment']['txn_id'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Lease Type</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['payment']['lease_type'] }}</span>
                                </div>
                                <div class="border-b border-gray-50"></div>

                                <div class="flex justify-between items-center group">
                                    <span class="text-gray-400 font-medium">Period Covered</span>
                                    <span class="font-bold text-[#0F172A]">{{ $data['payment']['period'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl overflow-hidden border border-gray-200 mb-6 i ">
                    <div class="bg-[#2563EB] px-6 py-3 flex justify-between items-center">
                        <span class="text-white text-xs font-bold uppercase tracking-wider">Description</span>
                        <span class="text-white text-xs font-bold uppercase tracking-wider">Amount</span>
                    </div>

                    <div class="bg-white px-6 py-4 flex justify-between items-center border-b border-gray-100 min-h-[60px]">
                        <span class="text-sm font-medium text-gray-800">{{ $data['financials']['description'] }}</span>
                        <span class="text-sm font-bold text-gray-800">₱ {{ number_format($data['financials']['amount'], 0) }}</span>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
                        <span class="text-[#2563EB] text-xs font-bold uppercase">Total Due</span>
                        <span class="text-[#2563EB] text-sm font-bold">₱ {{ number_format($data['financials']['amount'], 0) }}</span>
                    </div>
                </div>

                <div class="w-full bg-gradient-to-r from-[#1D4ED8] to-[#3B82F6] rounded-xl p-6 text-center text-white mb-8 shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs font-semibold uppercase tracking-widest opacity-90 mb-1">Total Amount Paid</p>
                        <p class="text-4xl font-extrabold tracking-tight">₱ {{ number_format($data['financials']['amount'], 0) }}</p>
                    </div>
                    <div class="absolute top-0 right-0 w-32 h-full bg-white/10 -skew-x-12 translate-x-10 pointer-events-none"></div>
                </div>

                <div class="flex justify-end">
                    <button wire:click="save" class="bg-[#00005C] hover:bg-[#000040] text-white text-sm font-semibold py-2.5 px-12 rounded-full shadow-lg transition-all transform hover:-translate-y-0.5">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>
