<?php

namespace App\Livewire\Layouts;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\RevenueForecastService;
use App\Models\Transaction;
use Carbon\Carbon;

class RevenueForecast extends Component
{
    public $forecastYear;
    public $monthlyForecasts = [];
    public $totalAnnualRevenue = 0;
    public $totalRemainingRevenue = 0;
    public $averageMonthlyRevenue = 0;
    public $loading = false;
    public $error = null;
    public $warning = null;
    public $isFallback = false;
    public $dataPointsUsed = 0;
    public $forecastLoaded = false;

    protected $revenueForecastService;

    public function boot(RevenueForecastService $revenueForecastService)
    {
        $this->revenueForecastService = $revenueForecastService;
    }

    public function mount()
    {
        $this->forecastYear = Carbon::now()->year;
    }

    public function loadForecast()
    {
        if ($this->forecastLoaded) {
            return;
        }

        $this->forecastLoaded = true;
        $this->generateForecast();
    }

    #[On('updateYear')]
    public function updateYear($year)
    {
        $this->forecastYear = $year;

        if (!$this->forecastLoaded) {
            $this->forecastLoaded = true;
        }

        $this->generateForecast();
    }

    public function generateForecast()
    {
        $this->loading = true;
        $this->error = null;
        $this->warning = null;
        $this->isFallback = false;
        $this->monthlyForecasts = [];
        $this->totalAnnualRevenue = 0;
        $this->totalRemainingRevenue = 0;
        $this->averageMonthlyRevenue = 0;
        $this->dataPointsUsed = 0;

        try {
            $result = $this->revenueForecastService->generateMonthlyForecast($this->forecastYear);
            
            $this->monthlyForecasts = $result['monthly_forecasts'];
            $this->totalAnnualRevenue = $result['total_annual_revenue'];
            $this->totalRemainingRevenue = $result['total_remaining_revenue'];
            $this->averageMonthlyRevenue = $result['average_monthly_revenue'];
            $this->dataPointsUsed = $result['data_points_used'] ?? 0;
            $this->isFallback = (bool)($result['is_fallback'] ?? false);
            $this->warning = $result['warning'] ?? null;
            
            // Add actual earnings data to each month
            if (!$this->isFallback) {
                $this->monthlyForecasts = $this->enrichForecastWithActualEarnings($this->monthlyForecasts);
            }
            
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->loading = false;
            $this->dispatch('revenue-forecast-updated');
        }
    }

    private function enrichForecastWithActualEarnings($forecasts)
    {
        if (empty($forecasts)) {
            return $forecasts;
        }

        $monthExpr = $this->transactionMonthExpression();
        $actualByMonth = Transaction::query()
            ->creditInflows()
            ->whereYear('transaction_date', $this->forecastYear)
            ->selectRaw("{$monthExpr} as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($forecasts as &$monthForecast) {
            $monthNumber = $monthForecast['month'] ?? null;

            if ($monthNumber) {
                $monthForecast['actual_revenue'] = (float) ($actualByMonth[$monthNumber] ?? 0);
            } else {
                $monthForecast['actual_revenue'] = 0;
            }
        }

        return $forecasts;
    }

    private function transactionMonthExpression(): string
    {
        $driver = Transaction::query()->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            return 'EXTRACT(MONTH FROM transaction_date)::int';
        }

        return 'MONTH(transaction_date)';
    }

    public function render()
    {
        return view('livewire.layouts.revenue-forecast');
    }
}