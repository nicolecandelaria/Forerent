<div wire:init="loadForecast">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Maintenance Cost Forecast</h2>
    </div>

    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Error:</strong> {{ $error }}
        </div>
    @endif

    @if($forecast && ($forecast['is_fallback'] ?? false))
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4">
            <strong>Notice:</strong> {{ $forecast['warning'] ?? 'Using fallback forecast data.' }}
        </div>
    @endif

    <script type="application/json" id="maintenanceForecastPayload">{!! json_encode([
        'forecastYear' => $year,
        'monthlyForecasts' => $forecast['monthly_forecasts'] ?? [],
    ]) !!}</script>

    @if($hasData && $maintenanceStats && $forecast && isset($forecast['success']) && $forecast['success'])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 shadow-sm">
                <p class="text-sm font-medium text-blue-700 mb-2">Annual Forecast</p>
                <p class="text-3xl font-bold text-blue-600">₱{{ number_format($forecast['total_annual_cost'] ?? 0, 0) }}</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 shadow-sm">
                <p class="text-sm font-medium text-blue-700 mb-2">Monthly Average</p>
                <p class="text-3xl font-bold text-blue-600">₱{{ number_format($forecast['average_monthly_cost'] ?? 0, 0) }}</p>
            </div>
        </div>
    @endif

    @if(!$forecastLoaded || $isGenerating)
        <div class="text-center py-16 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m0 0c0-1 1-3 3-3s3 1 3 3-1 3-3 3-3-1-3-3m0 0c0 1-1 3-3 3s-3-1-3-3 1-3 3-3 3 1 3 3" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-700">Generating Forecast</h3>
            <p class="mt-1 text-sm text-gray-500">Processing maintenance data...</p>
        </div>
    @elseif($forecast && isset($forecast['success']) && $forecast['success'])
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-stretch">
            <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-2" wire:ignore>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-[#070642]">Monthly Maintenance Costs</h3>
                    <x-dropdown label="Download" width="w-44" tooltip="Download chart as image or CSV">
                        <x-dropdown-item @click="open = false; window.maintenanceChartInstance?.dataURI().then(uri => { const a = document.createElement('a'); a.href = uri.imgURI; a.download = 'maintenance-costs-{{ $year }}.png'; a.click(); })">
                            Download PNG
                        </x-dropdown-item>
                        <x-dropdown-item @click="open = false; window.maintenanceChartInstance?.dataURI({type: 'image/svg+xml'}).then(uri => { const a = document.createElement('a'); a.href = uri.imgURI; a.download = 'maintenance-costs-{{ $year }}.svg'; a.click(); })">
                            Download SVG
                        </x-dropdown-item>
                        <x-dropdown-item @click="open = false; window.downloadMaintenanceCsv()">
                            Download CSV
                        </x-dropdown-item>
                    </x-dropdown>
                </div>
                <div class="flex items-center gap-5 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-sm" style="background-color: #8CC5FF;"></span>
                        <span class="text-sm text-gray-500 font-medium">Actual Monthly Cost</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-sm" style="background-color: #0C0B50;"></span>
                        <span class="text-sm text-gray-500 font-medium">Forecasted Cost</span>
                    </div>
                </div>
                <div id="maintenanceChart" style="height: 400px;"></div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-1 flex flex-col" wire:ignore>
                <h3 class="text-xl font-bold text-[#070642] mb-2">Job Count Forecast</h3>
                <p class="text-sm text-gray-500 mb-4">Forecast vs actual split per month</p>
                <div class="flex items-center gap-5 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-sm" style="background-color: #0C0B50;"></span>
                        <span class="text-xs text-gray-500 font-medium">Forecasted</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-sm" style="background-color: #8CC5FF;"></span>
                        <span class="text-xs text-gray-500 font-medium">Actual</span>
                    </div>
                </div>
                <div id="jobCountChart" class="flex-1" style="height: 400px;"></div>
            </div>
        </div>
    @else
        <div class="text-center py-16 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-700">No Forecast Data</h3>
            <p class="mt-1 text-sm text-gray-500">Unable to generate forecast. Please ensure maintenance records exist.</p>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function getMaintenanceForecastPayload() {
            const payloadNode = document.getElementById('maintenanceForecastPayload');

            if (!payloadNode) {
                return { forecastYear: new Date().getFullYear(), monthlyForecasts: [] };
            }

            try {
                return JSON.parse(payloadNode.textContent || '{}');
            } catch (error) {
                return { forecastYear: new Date().getFullYear(), monthlyForecasts: [] };
            }
        }

        function destroyMaintenanceCharts() {
            if (window.maintenanceChartInstance) {
                window.maintenanceChartInstance.destroy();
                window.maintenanceChartInstance = null;
            }

            if (window.jobCountChartInstance) {
                window.jobCountChartInstance.destroy();
                window.jobCountChartInstance = null;
            }
        }

        function renderMaintenanceCharts() {
            if (typeof ApexCharts === 'undefined') {
                setTimeout(renderMaintenanceCharts, 100);
                return;
            }

            const maintenanceChartElement = document.getElementById('maintenanceChart');
            const jobCountChartElement = document.getElementById('jobCountChart');
            const payload = getMaintenanceForecastPayload();
            const monthlyForecasts = Array.isArray(payload.monthlyForecasts) ? payload.monthlyForecasts : [];

            if (!maintenanceChartElement || !jobCountChartElement || monthlyForecasts.length === 0) {
                destroyMaintenanceCharts();
                return;
            }

            const categories = monthlyForecasts.map(f => f.month_name);
            const forecastCostData = monthlyForecasts.map(f => Number(f.forecasted_cost || 0));
            const actualCostData = monthlyForecasts.map(f => Number(f.actual_cost || 0));
            const forecastJobCounts = monthlyForecasts.map(f => Math.round(Number(f.maintenance_count_estimate || 0)));
            const actualJobCounts = monthlyForecasts.map(f => Math.round(Number(f.actual_job_count || 0)));

            const costSeries = [
                {
                    name: 'Forecasted Cost',
                    data: forecastCostData,
                    color: '#0C0B50'
                },
                {
                    name: 'Actual Cost',
                    data: actualCostData,
                    color: '#2563EB'
                }
            ];

            const jobShareSeries = [
                {
                    name: 'Forecasted Jobs',
                    data: forecastJobCounts.map((forecastCount, index) => {
                        const actualCount = actualJobCounts[index] || 0;
                        const total = forecastCount + actualCount;
                        return total > 0 ? (forecastCount / total) * 100 : 0;
                    })
                },
                {
                    name: 'Actual Jobs',
                    data: actualJobCounts.map((actualCount, index) => {
                        const forecastCount = forecastJobCounts[index] || 0;
                        const total = forecastCount + actualCount;
                        return total > 0 ? (actualCount / total) * 100 : 0;
                    })
                }
            ];

            const costOptions = {
                chart: {
                    type: 'line',
                    height: 400,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: [4, 4],
                    curve: 'smooth',
                    lineCap: 'round',
                    colors: ['#0C0B50', '#2563EB']
                },
                markers: {
                    size: [2, 2],
                    colors: ['#0C0B50', '#2563EB'],
                    strokeColors: ['#FFFFFF', '#FFFFFF'],
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#6B7280',
                            fontFamily: 'Open Sans, sans-serif'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return '₱' + Number(val).toLocaleString();
                        },
                        style: {
                            fontSize: '12px',
                            colors: '#6B7280',
                            fontFamily: 'Open Sans, sans-serif'
                        }
                    }
                },
                legend: {
                    show: false
                },
                fill: {
                    type: 'solid',
                    opacity: [0, 0]
                },
                colors: ['#0C0B50', '#2563EB'],
                theme: {
                    monochrome: {
                        enabled: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return '₱' + Number(val).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        }
                    },
                    theme: 'dark'
                },
                grid: {
                    borderColor: '#F3F4F6',
                    strokeDashArray: 0,
                    show: true,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                states: {
                    hover: {
                        filter: {
                            type: 'darken',
                            value: 0.1
                        }
                    },
                    active: {
                        filter: {
                            type: 'darken',
                            value: 0.1
                        }
                    }
                }
            };

            destroyMaintenanceCharts();

            window.maintenanceChartInstance = new ApexCharts(
                maintenanceChartElement,
                {
                    series: costSeries,
                    ...costOptions
                }
            );

            window.maintenanceChartInstance.render().then(() => {
                // Force explicit stroke colors in the generated SVG to avoid washed-out line rendering.
                const lineColors = ['#0C0B50', '#2563EB'];
                const linePaths = maintenanceChartElement.querySelectorAll('.apexcharts-line-series .apexcharts-line');

                linePaths.forEach((path, index) => {
                    const color = lineColors[index] || lineColors[0];
                    path.setAttribute('stroke', color);
                    path.setAttribute('stroke-opacity', '1');
                    path.style.stroke = color;
                    path.style.opacity = '1';
                });
            });

            const jobCountOptions = {
                chart: {
                    type: 'bar',
                    height: 400,
                    stacked: true,
                    stackType: '100%',
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        speed: 700
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 5,
                        barHeight: '60%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: categories,
                    min: 0,
                    max: 100,
                    tickAmount: 5,
                    labels: {
                        formatter: function (val) {
                            return Math.round(Number(val)) + '%';
                        },
                        style: {
                            fontSize: '12px',
                            colors: '#6B7280'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '11px',
                            colors: '#6B7280'
                        }
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#FFFFFF']
                },
                fill: {
                    opacity: 1
                },
                legend: {
                    show: false
                },
                colors: ['#0C0B50', '#8CC5FF'],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val, { seriesIndex, dataPointIndex }) {
                            const percentage = Number(val || 0).toFixed(1);
                            const forecastCount = forecastJobCounts[dataPointIndex] || 0;
                            const actualCount = actualJobCounts[dataPointIndex] || 0;

                            if (seriesIndex === 0) {
                                return `${percentage}% (${forecastCount} jobs)`;
                            }

                            return `${percentage}% (${actualCount} jobs)`;
                        }
                    },
                    theme: 'dark'
                },
                grid: {
                    borderColor: '#F3F4F6',
                    strokeDashArray: 0,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: false } }
                }
            };

            window.jobCountChartInstance = new ApexCharts(
                jobCountChartElement,
                {
                    series: jobShareSeries,
                    ...jobCountOptions
                }
            );

            window.jobCountChartInstance.render();
        }

        window.downloadMaintenanceCsv = function() {
            const payload = getMaintenanceForecastPayload();
            const monthlyForecasts = Array.isArray(payload.monthlyForecasts) ? payload.monthlyForecasts : [];
            const forecastYear = Number(payload.forecastYear || new Date().getFullYear());

            if (monthlyForecasts.length === 0) {
                return;
            }

            const csv = ['Month,Forecasted Cost,Actual Cost,Forecasted Jobs,Actual Jobs'];
            monthlyForecasts.forEach(m => {
                csv.push(
                    `${m.month_name},${Number(m.forecasted_cost || 0)},${Number(m.actual_cost || 0)},${Math.round(Number(m.maintenance_count_estimate || 0))},${Math.round(Number(m.actual_job_count || 0))}`
                );
            });

            const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `maintenance-costs-${forecastYear}.csv`;
            a.click();
        };

        const scheduleMaintenanceChartRender = () => setTimeout(renderMaintenanceCharts, 0);

        if (!window.__maintenanceForecastListenersBound) {
            window.__maintenanceForecastListenersBound = true;

            document.addEventListener('DOMContentLoaded', scheduleMaintenanceChartRender);
            document.addEventListener('livewire:navigated', scheduleMaintenanceChartRender);

            document.addEventListener('livewire:init', () => {
                Livewire.on('maintenance-forecast-updated', scheduleMaintenanceChartRender);
            });
        }

        scheduleMaintenanceChartRender();
    </script>
</div>
