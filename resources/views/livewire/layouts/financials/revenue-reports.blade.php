<div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#070642]">Financial Overview</h2>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        {{-- Financial Inflows and Outflows (Same theme as dashboard) --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-[#070642]">Financial Inflows and Outflows</h3>
            </div>
            <div wire:ignore id="inflowOutflowChart" style="height: 350px;"></div>
        </div>

        {{-- Maintenance Cost Breakdown Per Category --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h3 class="text-xl font-bold text-[#070642]">Maintenance Expenses Breakdown</h3>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $maintenanceBreakdownLabel }}</span>
                    <select
                        id="maintenanceScope"
                        wire:model.live="maintenanceBreakdownScope"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700"
                    >
                        <option value="month">Current Month</option>
                        <option value="year">Whole Year</option>
                    </select>
                </div>
            </div>
            <div wire:ignore id="maintenanceBreakdownChart" style="height: 340px;"></div>
        </div>
    </div>

    <script type="application/json" id="revenueReportsPayload">{!! json_encode([
        'inflowOutflowData' => $inflowOutflowData,
        'maintenanceCostData' => $maintenanceCostData,
    ]) !!}</script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function renderRevenueReportCharts(payload) {
            if (!payload || !payload.inflowOutflowData || !payload.maintenanceCostData) {
                return;
            }

            const inflowNode = document.querySelector('#inflowOutflowChart');
            const maintenanceNode = document.querySelector('#maintenanceBreakdownChart');
            if (!inflowNode || !maintenanceNode) {
                return;
            }

            window.revenueReportCharts = window.revenueReportCharts || {
                inflowOutflow: null,
                maintenanceBreakdown: null,
            };

            const commonChartOptions = {
                toolbar: { show: false },
                parentHeightOffset: 0,
            };

            const inflowOptions = {
                ...commonChartOptions,
                series: [
                    { name: 'Revenue', data: payload.inflowOutflowData.income },
                    { name: 'Expenses', data: payload.inflowOutflowData.expenses }
                ],
                chart: { type: 'line', height: 350, toolbar: { show: false } },
                stroke: { curve: 'smooth', width: [4, 4] },
                markers: { size: 4, hover: { size: 6 } },
                colors: ['#2B66F5', '#F5652B'],
                dataLabels: { enabled: false },
                xaxis: { categories: payload.inflowOutflowData.labels },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return '₱' + (val / 1000).toFixed(0) + 'k';
                        }
                    }
                },
                legend: { position: 'top', horizontalAlign: 'right', labels: { colors: '#070642' } },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(val) {
                            return '₱' + Number(val).toLocaleString();
                        }
                    }
                },
                grid: { borderColor: '#E5E7EB', strokeDashArray: 3 }
            };

            if (window.revenueReportCharts.inflowOutflow) {
                window.revenueReportCharts.inflowOutflow.destroy();
            }
            window.revenueReportCharts.inflowOutflow = new ApexCharts(inflowNode, inflowOptions);
            window.revenueReportCharts.inflowOutflow.render();

            const maintenanceOptions = {
                ...commonChartOptions,
                series: payload.maintenanceCostData.amounts,
                chart: { type: 'donut', height: 340, toolbar: { show: false } },
                labels: payload.maintenanceCostData.labels,
                colors: ['#2B66F5', '#4D7DF8', '#6F97FA', '#90B1FC', '#B2CBFF'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Math.round(val) + '%';
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function() {
                                        const total = (payload.maintenanceCostData.amounts || []).reduce((sum, value) => sum + Number(value || 0), 0);
                                        return '₱' + total.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: { colors: '#070642' }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return '₱' + Number(val).toLocaleString();
                        }
                    }
                },
                noData: {
                    text: 'No maintenance expenses data'
                }
            };

            if (window.revenueReportCharts.maintenanceBreakdown) {
                window.revenueReportCharts.maintenanceBreakdown.destroy();
            }
            window.revenueReportCharts.maintenanceBreakdown = new ApexCharts(maintenanceNode, maintenanceOptions);
            window.revenueReportCharts.maintenanceBreakdown.render();
        }

        function bootRevenueReportCharts() {
            if (typeof ApexCharts === 'undefined') {
                setTimeout(bootRevenueReportCharts, 100);
                return;
            }

            const payloadNode = document.getElementById('revenueReportsPayload');
            if (payloadNode) {
                try {
                    renderRevenueReportCharts(JSON.parse(payloadNode.textContent || '{}'));
                } catch (e) {
                    console.error('Failed to parse revenue reports payload', e);
                }
            }

            if (!window.__revenueReportsChartsListenerBound) {
                window.__revenueReportsChartsListenerBound = true;
                Livewire.on('update-charts', (event) => {
                    const payload = Array.isArray(event) ? event[0] : event;
                    renderRevenueReportCharts(payload);
                });
            }

        }

        document.addEventListener('DOMContentLoaded', bootRevenueReportCharts);
        document.addEventListener('livewire:navigated', bootRevenueReportCharts);
    </script>
</div>
