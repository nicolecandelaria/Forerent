<?php
// app/Livewire/Layouts/MaintForecast.php

namespace App\Livewire\Layouts;

use Livewire\Component;
use App\Services\MaintenanceForecast;
use Illuminate\Support\Facades\Log;

class MaintForecast extends Component
{
    public $year;
    public $forecast = null;
    public $maintenanceStats = null;
    public $isGenerating = false;
    public $error = null;
    public $hasData = false;
    public $debugInfo = null;
    public $forecastLoaded = false;

    protected $rules = [
        'year' => 'required|integer|min:2023|max:2030'
    ];

    public function mount()
    {
        $this->year = date('Y');
        $this->loadStats();
    }

    public function loadForecast()
    {
        if ($this->forecastLoaded) {
            return;
        }

        $this->forecastLoaded = true;
        $this->generateForecast();
    }

    public function updateYear($year)
    {
        $this->year = (int) $year;

        if (!$this->forecastLoaded) {
            $this->forecastLoaded = true;
        }

        $this->generateForecast();
    }

    public function loadStats()
    {
        try {
            $service = app(MaintenanceForecast::class);
            $stats = $service->getMaintenanceStats();

            $this->maintenanceStats = [
                'total_records' => $stats['total_records'] ?? 0,
                'date_range' => $stats['date_range'] ?? 'No data available',
                'total_cost' => $stats['total_cost'] ?? 0,
                'avg_monthly_cost' => $stats['avg_monthly_cost'] ?? 0
            ];

            $this->hasData = ($this->maintenanceStats['total_records'] ?? 0) > 0;
        } catch (\Exception $e) {
            $this->maintenanceStats = [
                'total_records' => 0,
                'date_range' => 'Error',
                'total_cost' => 0,
                'avg_monthly_cost' => 0
            ];
            $this->hasData = false;
            $this->error = 'Failed to load maintenance stats: ' . $e->getMessage();
        }
    }

    public function generateForecast()
    {
        Log::info('=== FORECAST GENERATION STARTED ===');
        $this->validate();
        $this->isGenerating = true;
        $this->error = null;
        $this->forecast = null;
        $this->debugInfo = null;

        try {
            $service = app(MaintenanceForecast::class);
            
            // 1. Get the RAW data for the API
            $maintenanceData = $service->getMaintenanceDataForForecast();
            
            if (empty($maintenanceData)) {
                throw new \Exception('No maintenance data found to send to the API.');
            }

            Log::info('Raw data for API', ['record_count' => count($maintenanceData)]);

            // 2. Call the API with the raw data
            $this->forecast = $service->generateForecast($this->year, $maintenanceData);

            // 3. Process the response
            if (!is_array($this->forecast)) {
                throw new \Exception('Invalid forecast response format: ' . gettype($this->forecast));
            }

            if (isset($this->forecast['success']) && $this->forecast['success'] === false) {
                $this->debugInfo = $this->forecast['debug_info'] ?? null;
                throw new \Exception($this->forecast['error'] ?? 'Forecast generation failed');
            }

            if (!isset($this->forecast['monthly_forecasts']) || empty($this->forecast['monthly_forecasts'])) {
                throw new \Exception('Forecast response missing monthly_forecasts');
            }

            Log::info('✅ FORECAST GENERATED SUCCESSFULLY');

        } catch (\Exception $e) {
            Log::error('❌ Forecast generation failed', [
                'error' => $e->getMessage(),
            ]);
            $this->error = 'Failed to generate forecast: ' . $e->getMessage();
            $this->forecast = null;
        } finally {
            $this->isGenerating = false;
            $this->dispatch('maintenance-forecast-updated');
        }

        Log::info('=== FORECAST GENERATION COMPLETED ===');
    }

    public function render()
    {
        // This line tells Laravel to load the file at:
        // resources/views/livewire/layouts/maint-forecast.blade.php
        return view('livewire.layouts.maint-forecast');
    }
}