<div class="bg-white rounded-2xl p-6 shadow-lg h-full">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-xl font-bold text-[#070642]">Rent Collection Summary</h3>
            <p class="text-xs text-gray-500 mt-1">Values shown for {{ $rentSummaryPeriodLabel }}</p>
        </div>
        <div class="w-full sm:w-44">
            <label for="rentSummaryMonth" class="sr-only">Select month</label>
            <select id="rentSummaryMonth" wire:model.live="rentSummaryMonth" class="w-full rounded-lg border-gray-300 text-sm focus:border-[#2B66F5] focus:ring-[#2B66F5]">
                @foreach($rentSummaryMonthOptions as $monthKey => $monthLabel)
                    <option value="{{ $monthKey }}">{{ $monthLabel }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="flex flex-col gap-4">
        <!-- Amount Display -->
        <div class="space-y-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Collected Rent ({{ $rentSummaryPeriodLabel }})</p>
                <div class="flex items-baseline justify-between gap-3">
                    <span class="text-2xl sm:text-3xl font-bold text-[#2B66F5] leading-none whitespace-nowrap">₱ {{ number_format($totalRentCollected, 2) }}</span>
                    <span class="text-sm text-gray-600 whitespace-nowrap">PHP</span>
                </div>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Uncollected Rent ({{ $rentSummaryPeriodLabel }})</p>
                <div class="flex items-baseline justify-between gap-3">
                    <span class="text-2xl sm:text-3xl font-bold text-[#F5652B] leading-none whitespace-nowrap">₱ {{ number_format($totalUncollectedRent, 2) }}</span>
                    <span class="text-sm text-gray-600 whitespace-nowrap">PHP</span>
                </div>
            </div>
        </div>

        <!-- Collection Ratio Donut (same totals as amounts above) -->
        <div class="space-y-3">
            <div class="flex items-center gap-4">
                <div class="relative w-16 h-16">
                    <svg viewBox="0 0 36 36" class="w-16 h-16 -rotate-90">
                        <circle cx="18" cy="18" r="15.915" fill="none" stroke="#F5652B" stroke-width="4"></circle>
                        <circle
                            cx="18"
                            cy="18"
                            r="15.915"
                            fill="none"
                            stroke="#2B66F5"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-dasharray="{{ round($rentCollectedPercentage, 1) }} 100"
                        ></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-[10px] font-semibold text-[#070642]">{{ round($rentCollectedPercentage, 0) }}%</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-1 text-xs text-gray-600">
                    <p>
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#2B66F5] mr-1"></span>
                        Collected Rent
                    </p>
                    <p>
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#F5652B] mr-1"></span>
                        Uncollected Rent
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs text-gray-600">
                <p>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#2B66F5] mr-1"></span>
                    Collected: {{ round($rentCollectedPercentage, 1) }}% (₱ {{ number_format($totalRentCollected, 2) }})
                </p>
                <p>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#F5652B] mr-1"></span>
                    Uncollected: {{ round($uncollectedPercentage, 1) }}% (₱ {{ number_format($totalUncollectedRent, 2) }})
                </p>
            </div>
        </div>

        <!-- Stats -->
        <div class="pt-2 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                <span class="font-semibold text-green-600">{{ round($rentCollectedPercentage, 1) }}%</span>
                of total expected rent in {{ $rentSummaryPeriodLabel }} is collected
            </p>
        </div>
    </div>
</div>
