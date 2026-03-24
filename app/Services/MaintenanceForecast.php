<?php
// app/Services/MaintenanceForecast.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MaintenanceForecast
{
    protected $apiBaseUrl;

    public function __construct()
    {
        // Use FASTAPI_URL instead of PYTHON_API_URL
        $this->apiBaseUrl = env('FASTAPI_URL', 'http://localhost:8000');
        \Log::info('API Base URL: ' . $this->apiBaseUrl);
    }

    public function generateForecast($year, $maintenanceData)
    {
        try {

            Http::timeout(60)->get("{$this->apiBaseUrl}/");

            
            $csvData = $this->convertToCsv($maintenanceData);

            Log::info("Sending maintenance forecast request for year: {$year}");

            $response = Http::timeout(120)->post($this->apiBaseUrl . '/api/forecast/maintenance', [
                'csv_data' => $csvData,
                'year' => $year
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info("Maintenance forecast API response received", $result);
                return $result;
            } else {
                $error = 'Maintenance forecast API error: ' . $response->body();
                Log::error($error);
                throw new \Exception($error);
            }
        } catch (\Exception $e) {
            Log::error('Maintenance forecast service error: ' . $e->getMessage());

            // Return a fallback response if API fails
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'monthly_forecasts' => [],
                'maintenance_schedule' => [],
                'total_annual_cost' => 0,
                'total_remaining_cost' => 0,
                'average_monthly_cost' => 0,
                'data_points_used' => 0,
                'model_performance' => [
                    'r2_score' => 0,
                    'mae' => 0,
                    'mape' => 0,
                    'clusters_used' => 0
                ]
            ];
        }
    }

    private function convertToCsv($data)
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        // Check if data is in monthly format or individual record format
        $firstRow = $data[0];

        if (isset($firstRow['monthly_maintenance_cost'])) {
            // Monthly aggregated format
            $headers = ['year', 'month', 'monthly_maintenance_cost', 'maintenance_count', 'urgency_score'];
        } else {
            // Individual record format (original)
            $headers = array_keys($firstRow);
        }

        // Write headers
        fputcsv($output, $headers);

        // Write data
        foreach ($data as $row) {
            if (isset($firstRow['monthly_maintenance_cost'])) {
                // Write only the monthly aggregated fields
                fputcsv($output, [
                    $row['year'] ?? '',
                    $row['month'] ?? '',
                    $row['monthly_maintenance_cost'] ?? '',
                    $row['maintenance_count'] ?? '',
                    $row['urgency_score'] ?? ''
                ]);
            } else {
                // Write all fields (original format)
                fputcsv($output, $row);
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        \Log::info('CSV data prepared', [
            'rows' => count($data),
            'csv_length' => strlen($csv),
            'format' => isset($firstRow['monthly_maintenance_cost']) ? 'monthly' : 'individual'
        ]);

        return $csv;
    }

    public function getMaintenanceDataForForecast()
    {
        // Fetch ALL raw, individual completed logs from all time.
        // The Python API needs the full history to do the aggregation itself.
        $rawData = DB::table('maintenance_requests as mr')
            ->join('maintenance_logs as ml', 'mr.request_id', '=', 'ml.request_id')
            ->select(
                'ml.log_id',
                'ml.completion_date as date', // Python will use this for the date
                'ml.cost',                    // Python will use this for cost
                'mr.urgency',                  // Python will use this
                'mr.category'
            )
            ->where('mr.status', 'Completed')
            ->whereNotNull('ml.completion_date')
            ->whereNotNull('ml.cost')
            ->orderBy('ml.completion_date')
            ->get()
            ->map(function ($item) {
                // Convert stdClass to array
                return (array)$item;
            })
            ->toArray();

        \Log::info('Sending ' . count($rawData) . ' total raw records to API for training.');

        return $rawData;
    }
    private function getMonthlyDateRange($monthlyData)
    {
        if (empty($monthlyData)) {
            return 'No data';
        }

        $first = $monthlyData[0];
        $last = $monthlyData[count($monthlyData) - 1];

        return $first['year'] . '-' . $first['month'] . ' to ' . $last['year'] . '-' . $last['month'];
    }

    public function getMaintenanceStats()
    {
        $stats = DB::table('maintenance_requests as mr')
            ->join('maintenance_logs as ml', 'mr.request_id', '=', 'ml.request_id')
            ->select(
                DB::raw('COUNT(*) as total_records'),
                DB::raw('MIN(mr.log_date) as earliest_date'),
                DB::raw('MAX(mr.log_date) as latest_date'),
                DB::raw('SUM(ml.cost) as total_cost'),
                DB::raw('AVG(ml.cost) as avg_cost')
            )
            ->where('mr.status', 'Completed')
            ->first();

        return [
            'total_records' => $stats->total_records ?? 0,
            'date_range' => ($stats->earliest_date ?? 'N/A') . ' to ' . ($stats->latest_date ?? 'N/A'),
            'total_cost' => $stats->total_cost ?? 0,
            'avg_monthly_cost' => $stats->avg_cost ?? 0
        ];
    }

    private function inferCategoryFromProblem($requestId)
    {
        // Temporary solution - you should add a 'category' column to maintenance_request table
        $categories = ['Plumbing', 'Electrical', 'Structural', 'Appliance', 'Pest Control'];
        return $categories[array_rand($categories)];
    }
}
