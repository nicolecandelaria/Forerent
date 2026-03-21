<div class="bg-white rounded-2xl p-6 shadow-lg h-full flex flex-col" wire:ignore>
    <h3 class="text-xl font-bold text-[#070642] mb-6">Revenue vs Expenses</h3>
    
    <div class="relative flex-1 min-h-80">
        <canvas id="revenueExpensesChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initRevenueExpensesChart() {
            const ctx = document.getElementById('revenueExpensesChart');
            if (!ctx) return;

            if (window.myRevenueExpensesChart) {
                window.myRevenueExpensesChart.destroy();
            }

            const labels = @json($monthlyLabels);
            const revenueData = @json($monthlyRevenue);
            const expensesData = @json($monthlyExpenses);

            window.myRevenueExpensesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Revenue',
                            data: revenueData,
                            borderColor: '#2B66F5',
                            backgroundColor: 'rgba(43, 102, 245, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#2B66F5',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Expenses',
                            data: expensesData,
                            borderColor: '#F5652B',
                            backgroundColor: 'rgba(245, 101, 43, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#F5652B',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: '500'
                                },
                                color: '#070642'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#070642',
                            bodyColor: '#070642',
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₱ ' + new Intl.NumberFormat('en-PH').format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return '₱' + (value / 1000).toFixed(0) + 'k';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initRevenueExpensesChart);
        document.addEventListener('livewire:navigated', initRevenueExpensesChart);

        document.addEventListener('livewire:init', () => {
            Livewire.on('dashboard-refresh-charts', () => {
                setTimeout(initRevenueExpensesChart, 0);
            });
        });
    </script>
</div>
