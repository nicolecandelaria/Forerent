<?php

namespace Database\Seeders;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceLog;
use App\Models\Lease;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Seeder;

class ManagerCurrentMonthMaintenanceSeeder extends Seeder
{
    protected Generator $faker;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->faker = app(Generator::class);

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $managerId = 2; // Manager ID

        // Get all leases for units managed by this manager
        $leases = Lease::query()
            ->join('beds', 'leases.bed_id', '=', 'beds.bed_id')
            ->join('units', 'beds.unit_id', '=', 'units.unit_id')
            ->where('units.manager_id', $managerId)
            ->select('leases.*')
            ->get();

        if ($leases->isEmpty()) {
            $this->command->info("No leases found for manager ID {$managerId}");
            return;
        }

        // Create 10 maintenance requests
        for ($i = 1; $i <= 10; $i++) {
            $lease = $leases->random();

            $logDate = Carbon::create($currentYear, $currentMonth, rand(1, 28));

            // Randomly decide if request is Pending or Completed
            $status = rand(0, 1) ? 'Completed' : 'Pending';

            $maintenanceRequest = MaintenanceRequest::create([
                'lease_id'      => $lease->lease_id,
                'log_date'      => $logDate,
                'logged_by'     => 'Seeder User',
                'problem'       => "Test maintenance issue #$i",
                'status'        => $status,
                'ticket_number' => 'TICKET-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'urgency'       => 'Level 2',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Only create maintenance log for Completed requests
            if ($status === 'Completed') {
                MaintenanceLog::create([
                    'request_id'      => $maintenanceRequest->request_id,
                    'completion_date' => $logDate->copy()->addDays(rand(1, 5)),
                    'cost'            => rand(500, 5000),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        $this->command->info("Created 10 maintenance requests with mixed statuses for manager ID {$managerId}");
    }
}
