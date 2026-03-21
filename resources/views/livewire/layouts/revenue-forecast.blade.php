<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gross Revenue Forecast</h2>
    </div>

    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Error:</strong> {{ $error }}
        </div>
    @endif

    @if(!empty($monthlyForecasts))
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-stretch">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 xl:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Monthly Revenue Forecast - {{ $forecastYear }}</h3>
                <div id="revenueChart" style="height: 400px;"></div>
            </div>

            <!-- Summary Cards (stacked right) -->
            <div class="flex flex-col gap-4">
                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 shadow-sm">
                    <p class="text-sm font-medium text-green-700 mb-2">Annual Forecast</p>
                    <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalAnnualRevenue, 0) }}</p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 shadow-sm">
                    <p class="text-sm font-medium text-blue-700 mb-2">Remaining Revenue</p>
                    <p class="text-3xl font-bold text-blue-600">₱{{ number_format($totalRemainingRevenue, 0) }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6 shadow-sm">
                    <p class="text-sm font-medium text-purple-700 mb-2">Monthly Average</p>
                    <p class="text-3xl font-bold text-purple-600">₱{{ number_format($averageMonthlyRevenue, 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Chart.js/ApexCharts Script -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('livewire:navigated', () => { renderRevenueChart(); });
            document.addEventListener('DOMContentLoaded', () => { renderRevenueChart(); });

            function renderRevenueChart() {
                const monthlyForecasts = @json($monthlyForecasts);
                
                if (!monthlyForecasts || monthlyForecasts.length === 0) return;

                const categories = monthlyForecasts.map(f => f.month_name);
                const series = [
                    {
                        type: 'line',
                        name: 'Actual Earnings',
                        data: monthlyForecasts.map(f => Number(f.actual_revenue || 0))
                    },
                    {
                        type: 'line',
                        name: 'Forecasted Revenue',
                        data: monthlyForecasts.map(f => Number(f.forecasted_revenue || 0))
                    }
                ];

                const options = {
                    chart: {
                        type: 'line',
                        height: 400,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true
                            }
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
                    stroke: {
                        curve: 'straight',
                        width: [4, 4],
                        lineCap: 'round',
                        dashArray: [0, 0]
                    },
                    markers: {
                        size: 4,
                        strokeWidth: 0,
                        hover: {
                            size: 6
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: {
                                fontSize: '12px',
                                colors: '#6B7280'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Revenue (₱)',
                            style: {
                                fontSize: '13px',
                                fontWeight: 600,
                                color: '#070642'
                            }
                        },
                        labels: {
                            formatter: function (val) {
                                return '₱' + (val / 1000).toFixed(0) + 'K';
                            },
                            style: {
                                colors: '#6B7280'
                            }
                        }
                    },
                    colors: ['#2B66F5', '#F5652B'],
                    legend: {
                        labels: {
                            colors: '#070642'
                        }
                    },
                    tooltip: {
                        theme: 'light',
                        shared: true,
                        intersect: false,
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function (val) {
                                return '₱' + val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            }
                        }
                    },
                    grid: {
                        borderColor: '#E5E7EB',
                        strokeDashArray: 3,
                        show: true
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        },
                        active: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    theme: {
                        monochrome: {
                            enabled: false
                        }
                    }
                };

                // Destroy existing chart if it exists
                if (window.revenueChartInstance) {
                    window.revenueChartInstance.destroy();
                }

                // Create new chart
                window.revenueChartInstance = new ApexCharts(
                    document.getElementById('revenueChart'),
                    {
                        series,
                        ...options
                    }
                );
                
                window.revenueChartInstance.render();
            }
        </script>
    @else
        <div class="text-center py-16 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-700">Loading Forecast</h3>
            <p class="mt-1 text-sm text-gray-500">Generating revenue predictions...</p>
        </div>
    @endif
</div>