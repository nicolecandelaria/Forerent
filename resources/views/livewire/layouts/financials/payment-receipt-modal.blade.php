<div x-data="{ show: @entangle('isOpen').live }">
    {{-- Custom Scrollbar Styles for a cleaner look --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; margin-bottom: 10px; margin-top: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <div
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
    >
        @if(!empty($data))
        <div
            class="relative w-full max-w-[850px] bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
        >
            {{-- Header Section --}}
            <div class="relative shrink-0 w-full" style="height: 180px;">
                <div class="absolute inset-0 z-0">
                    <svg class="w-full h-full" viewBox="0 0 850 180" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="header_gradient" x1="0" y1="90" x2="850" y2="90" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#1E42B1"/>
                                <stop offset="0.278859" stop-color="#224BBA"/>
                                <stop offset="0.562514" stop-color="#2757C8"/>
                                <stop offset="0.923093" stop-color="#4A83E6"/>
                            </linearGradient>
                        </defs>

                        {{-- Base Dark Blue Gradient --}}
                        <path d="M0 24C0 10.7452 10.7452 0 24 0H826C839.255 0 850 10.7452 850 24V180H0V24Z" fill="url(#header_gradient)"/>

                        {{-- Wave 1 (Leftmost wave) --}}
                        <path d="M825.997 0C839.252 0 849.997 10.7452 849.997 24V180H577.87C555.964 154.48 543.141 123.571 543.141 90.2627C543.141 56.7259 556.138 25.6204 578.319 0H825.997Z" fill="white" fill-opacity="0.08"/>

                        {{-- Wave 2 (Rightmost wave) --}}
                        <path d="M826 0C839.255 0 850 10.7452 850 24V180H596.262C575.836 154.48 563.879 123.571 563.879 90.2627C563.879 56.726 575.998 25.6204 596.681 0H826Z" fill="white" fill-opacity="0.08"/>
                    </svg>
                </div>

                <div class="relative z-10 px-8 pt-8 h-full flex justify-between items-center text-white pb-4">
                    <div>
                        <h1 class="text-[32px] font-extrabold tracking-tight uppercase">PAYMENT RECEIPT</h1>
                    </div>

                    <div class="flex gap-8 text-[13px] mr-4 mt-2">
                        <div class="space-y-1.5 font-medium opacity-90">
                            <p>Invoice Number</p>
                            <p>Issued Date</p>
                            <p>Due Date</p>
                        </div>
                        <div class="space-y-1.5 font-bold">
                            <p>{{ $data['invoice_no'] }}</p>
                            <p>{{ $data['issued_date'] }}</p>
                            <p>{{ $data['due_date'] }}</p>
                        </div>
                    </div>

                    <button @click="show = false" class="absolute top-6 right-8 text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Main Content Section --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar mb-4 mt-2">
                <div class="px-8 pb-4 pt-6 font-['Open_Sans']">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                        {{-- Left Column: Tenant & Recipient Info --}}
                        <div class="space-y-6">
                            {{-- Tenant Information --}}
                            <div class="border border-[#E2E2E2] rounded-3xl p-6 shadow-sm">
                                <h3 class="text-[#1E3A8A] text-sm font-bold uppercase tracking-[-0.02em] mb-6">Tenant Information</h3>
                                <div class="flex flex-col">
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px]">Name</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['tenant']['name'] }}</span>
                                    </div>
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px]">Unit</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['tenant']['unit'] }}</span>
                                    </div>
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px]">Building</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['tenant']['building'] }}</span>
                                    </div>
                                    <div class="flex items-start pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px] pt-0.5">Address</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right leading-tight max-w-[200px] ml-auto">{{ $data['tenant']['address'] }}</span>
                                    </div>
                                    <div class="flex items-center pt-1">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px]">Contact</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['tenant']['contact'] }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Recipient Information --}}
                            <div class="border border-[#E2E2E2] rounded-3xl p-6 shadow-sm">
                                <h3 class="text-[#1E3A8A] text-sm font-bold uppercase tracking-[-0.02em] mb-6">Recipient Information</h3>
                                <div class="flex flex-col">
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px]">Name</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['recipient']['name'] }}</span>
                                    </div>
                                    <div class="flex items-center pt-1">
                                        <span class="w-[80px] text-gray-400 font-medium text-[13px]">Position</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['recipient']['position'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Payment Details --}}
                        <div>
                            <div class="border border-[#E2E2E2] rounded-3xl p-6 shadow-sm h-fit">
                                <h3 class="text-[#1E3A8A] text-sm font-bold uppercase tracking-[-0.02em] mb-6">Payment Details</h3>
                                <div class="flex flex-col">
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[100px] text-gray-400 font-medium text-[13px]">Date Paid</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['payment']['date_paid'] }}</span>
                                    </div>
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[100px] text-gray-400 font-medium text-[13px]">Transaction ID</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['payment']['txn_id'] }}</span>
                                    </div>
                                    <div class="flex items-center pb-2 border-b border-[#E2E2E2] mb-3">
                                        <span class="w-[100px] text-gray-400 font-medium text-[13px]">Lease Type</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['payment']['lease_type'] }}</span>
                                    </div>
                                    <div class="flex items-center pt-1">
                                        <span class="w-[100px] text-gray-400 font-medium text-[13px]">Period</span>
                                        <span class="flex-1 font-bold text-[#01295E] text-[15px] text-right">{{ $data['payment']['period'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Table --}}
                    <div class="rounded-xl overflow-hidden border border-gray-200 mb-6 font-sans">
                        <div class="bg-[#2563EB] px-6 py-3 flex justify-between items-center">
                            <span class="text-white text-xs font-bold uppercase tracking-wider">Description</span>
                            <span class="text-white text-xs font-bold uppercase tracking-wider">Amount</span>
                        </div>
                        <div class="bg-white px-6 py-4 flex justify-between items-center border-b border-gray-100 min-h-[60px]">
                            <span class="text-sm font-medium text-gray-800">{{ $data['financials']['description'] }}</span>
                            <span class="text-sm font-bold text-gray-800">₱ {{ number_format($data['financials']['amount'], 2) }}</span>
                        </div>
                    </div>

                    {{-- Total Amount Summary --}}
                    <div class="w-full bg-gradient-to-r from-[#1D4ED8] to-[#3B82F6] rounded-xl p-6 text-center text-white shadow-lg font-sans">
                        <p class="text-xs font-semibold uppercase tracking-widest opacity-90 mb-1">Total Amount Paid</p>
                        <p class="text-4xl font-extrabold tracking-tight">₱ {{ number_format($data['financials']['amount'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
