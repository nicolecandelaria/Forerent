<div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-[#070642]">Financial Overview</h2>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">

            {{-- Dropdown --}}
            <div class="w-full sm:w-auto">
                <x-dropdown label="Frequency" width="w-48" align="right">
                    <x-dropdown-item wire:click="$set('filter', 'monthly')" :active="$filter === 'monthly'">
                        Monthly
                    </x-dropdown-item>
                    <x-dropdown-item wire:click="$set('filter', 'yearly')" :active="$filter === 'yearly'">
                        Yearly
                    </x-dropdown-item>
                </x-dropdown>
            </div>

            {{-- Export Button --}}
            {{-- <button class="flex items-center justify-center gap-2 bg-white border border-gray-200 text-[#070642] hover:bg-gray-50 rounded-full px-6 py-2.5 font-semibold text-[16px] shadow-sm transition-all w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button> --}}
        </div>
    </div>

    {{-- Key Metrics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg">
            <h3 class="text-sm font-medium opacity-90 mb-2">Total Income</h3>
            <p class="text-3xl font-bold">₱ {{ number_format($totalIncome) }}</p>
            <p class="text-xs opacity-75 mt-1">+5% from last {{ $filter === 'monthly' ? 'month' : 'year' }}</p>
        </div>

        <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg">
            <h3 class="text-sm font-medium opacity-90 mb-2">Total Expenses</h3>
            <p class="text-3xl font-bold">₱ {{ number_format($totalExpenses) }}</p>
            <p class="text-xs opacity-75 mt-1">+2% from last {{ $filter === 'monthly' ? 'month' : 'year' }}</p>
        </div>

        <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg">
            <h3 class="text-sm font-medium opacity-90 mb-2">Net Operating Income</h3>
            <p class="text-3xl font-bold">₱ {{ number_format($netOperatingIncome) }}</p>
            <p class="text-xs opacity-75 mt-1">+3% from last {{ $filter === 'monthly' ? 'month' : 'year' }}</p>
        </div>
    </div>

    {{-- Total Income Chart --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Total Income</h3>
        </div>
        <div id="totalIncomeChart" style="height: 350px;"></div>
    </div>

    {{-- Financial Inflows and Outflows --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Financial Inflows And Outflows</h3>
        </div>
        <div id="inflowOutflowChart" style="height: 350px;"></div>
    </div>

    {{-- Maintenance & Projected Revenue Section --}}

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

        {{-- Maintenance Cost Breakdown --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Maintenance Cost Breakdown</h3>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-8">
                <div id="maintenanceDonutChart" class="w-full sm:w-auto flex justify-center"></div>

                <div class="space-y-4 w-full sm:w-auto">
                    @foreach($maintenanceCostData as $item)
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 mb-1">{{ $item['label'] }} Maintenance Cost</span>
                        <span class="text-xl font-bold text-gray-800">₱ {{ number_format($item['amount']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap justify-center gap-4 sm:gap-6 mt-6">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-[#070642]"></div>
                    <span class="text-sm text-gray-600">Unit Structure</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                    <span class="text-sm text-gray-600">Plumbing</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-blue-300"></div>
                    <span class="text-sm text-gray-600">Electrical</span>
                </div>
            </div>
        </div>

        {{-- Projected Revenue --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Projected Revenue</h3>
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lg font-semibold text-gray-700">Next {{ $filter === 'monthly' ? 'Month' : 'Year' }}'s Projected Revenue</h4>
                </div>
                <div id="projectedRevenueChart" style="height: 200px;"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-[#070642] rounded-xl p-4 text-white">
                    <p class="text-sm opacity-90 mb-1">Total Expenses</p>
                    <p class="text-2xl font-bold">₱ 120,000</p>
                </div>
                <div class="bg-[#070642] rounded-xl p-4 text-white">
                    <p class="text-sm opacity-90 mb-1">Net Revenue</p>
                    <p class="text-2xl font-bold">₱ 120,000</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let chart1, chart2, chart3, chart4;

            function initCharts(data) {
                // Common options to ensure charts resize correctly
                const commonChartOptions = {
                    toolbar: { show: false },
                    parentHeightOffset: 0,
                };

                // 1. Total Income Chart
                const incomeOptions = {
                    ...commonChartOptions,
                    series: [{ name: 'Income', data: data.incomeData.data }],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
                    colors: ['#070642'],
                    dataLabels: { enabled: false },
                    xaxis: { categories: data.incomeData.labels },
                    grid: { borderColor: '#f1f1f1' }
                };
                if(chart1) chart1.destroy();
                chart1 = new ApexCharts(document.querySelector("#totalIncomeChart"), incomeOptions);
                chart1.render();

                // 2. Inflow/Outflow Chart
                const inflowOptions = {
                    ...commonChartOptions,
                    series: [
                        { name: 'Total Income', data: data.inflowOutflowData.income },
                        { name: 'Total Expenses', data: data.inflowOutflowData.expenses }
                    ],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '70%' } },
                    colors: ['#070642', '#60a5fa'],
                    dataLabels: { enabled: false },
                    xaxis: { categories: data.inflowOutflowData.labels },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    grid: { borderColor: '#f1f1f1' }
                };
                if(chart2) chart2.destroy();
                chart2 = new ApexCharts(document.querySelector("#inflowOutflowChart"), inflowOptions);
                chart2.render();

                // 3. Maintenance Donut Chart
                const maintenanceValues = data.maintenanceCostData.map(item => item.value);
                const maintenanceLabels = data.maintenanceCostData.map(item => item.label);

                const maintenanceOptions = {
                    ...commonChartOptions,
                    series: maintenanceValues,
                    chart: { type: 'donut', height: 280 }, // Fixed height for consistency
                    labels: maintenanceLabels,
                    colors: ['#070642', '#3b82f6', '#93c5fd'],
                    legend: { show: false },
                    plotOptions: {
                        pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => '100%' } } } }
                    },
                    dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' }
                };
                if(chart3) chart3.destroy();
                chart3 = new ApexCharts(document.querySelector("#maintenanceDonutChart"), maintenanceOptions);
                chart3.render();

                // 4. Projected Revenue Chart
                const projectedOptions = {
                    ...commonChartOptions,
                    series: [
                        { name: 'Projected Revenue', data: data.projectedRevenueData.projected },
                        { name: 'Projected Net Revenue', data: data.projectedRevenueData.projectedNet }
                    ],
                    chart: { type: 'line', height: 200, toolbar: { show: false } },
                    colors: ['#93c5fd', '#070642'],
                    stroke: { width: 3, curve: 'smooth' },
                    xaxis: { categories: data.projectedRevenueData.labels, labels: { style: { fontSize: '10px' } } },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    grid: { borderColor: '#f1f1f1', strokeDashArray: 4 }
                };
                if(chart4) chart4.destroy();
                chart4 = new ApexCharts(document.querySelector("#projectedRevenueChart"), projectedOptions);
                chart4.render();
            }

            initCharts({
                incomeData: @js($incomeData),
                inflowOutflowData: @js($inflowOutflowData),
                maintenanceCostData: @js($maintenanceCostData),
                projectedRevenueData: @js($projectedRevenueData)
            });

            Livewire.on('update-charts', (event) => {
                initCharts(event[0]);
            });
        });
    </script>
    @endpush
</div>
