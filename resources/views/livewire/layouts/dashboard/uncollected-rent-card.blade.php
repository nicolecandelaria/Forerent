<div class="bg-white rounded-2xl p-6 shadow-lg">
    <h3 class="text-xl font-bold text-[#070642] mb-4">Uncollected Rent</h3>
    
    <div class="flex flex-col gap-4">
        <!-- Amount Display -->
        <div class="flex items-baseline gap-2">
            <span class="text-4xl font-bold text-[#F5652B]">₱ {{ number_format($totalUncollectedRent, 2) }}</span>
            <span class="text-sm text-gray-600">PHP</span>
        </div>

        <!-- Stats -->
        <div class="pt-2 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                <span class="font-semibold text-red-600">{{ round($uncollectedPercentage, 1) }}%</span>
                of total expected rent
            </p>
        </div>

        <!-- Alert Box -->
        <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
            <p class="text-xs text-orange-800">
                <span class="font-semibold">Action Required:</span> Follow up on unpaid rent to improve cash flow.
            </p>
        </div>
    </div>
</div>
