<?php

namespace Database\Seeders;

use Faker\Generator;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bed;
use App\Models\Lease;
use Carbon\Carbon;

class LeaseSeeder extends Seeder
{
    protected Generator $faker;

    public function run(): void
    {
<<<<<<< HEAD
        $this->faker = app(Generator::class);

        $tenants = User::where('role', 'tenant')->get();
        $maleTenants = $tenants->where('gender', 'Male')->values();
        $femaleTenants = $tenants->where('gender', 'Female')->values();
=======
        // Get Tricia (the main test tenant) and Marcus (the main test manager)
        $tricia = User::where('role', 'tenant')->where('email', 'tenant@example.com')->first();
        $marcus = User::where('role', 'manager')->where('email', 'manager@example.com')->first();
>>>>>>> e28df792833dd8577e952e78d9282e370a791ac6

        $availableBeds = Bed::where('status', 'Vacant')->with('unit')->get();
        $managedBeds = $availableBeds->filter(fn($bed) => !is_null($bed->unit->manager_id))->values();

<<<<<<< HEAD
        if ($managedBeds->isEmpty() || $tenants->isEmpty()) {
            return;
        }
=======
        // Assign Tricia to a bed under Marcus first
        if ($tricia && $marcus) {
            $triciaBed = $availableBeds->filter(function ($bed) use ($marcus, $tricia) {
                return $bed->unit->manager_id === $marcus->user_id
                    && ($bed->unit->occupants === 'Co-ed' || $bed->unit->occupants === $tricia->gender);
            })->first();

            if ($triciaBed) {
                Lease::factory()->create([
                    'tenant_id'        => $tricia->user_id,
                    'bed_id'           => $triciaBed->bed_id,
                    'contract_rate'    => $triciaBed->unit->price,
                    'advance_amount'   => $triciaBed->unit->price,
                    'security_deposit' => $triciaBed->unit->price,
                ]);
                $triciaBed->update(['status' => 'Occupied']);
                $availableBeds = $availableBeds->reject(fn($b) => $b->bed_id === $triciaBed->bed_id);
            }
        }

        // Get remaining tenants (exclude Tricia since she's already assigned)
        $tenants = User::where('role', 'tenant')
            ->where('email', '!=', 'tenant@example.com')
            ->get();

        foreach ($tenants as $tenant) {
>>>>>>> e28df792833dd8577e952e78d9282e370a791ac6

        // Target occupancy: at least 60% of currently available managed beds.
        $targetOccupiedCount = (int) ceil($managedBeds->count() * 0.60);
        $bedsToOccupy = $managedBeds->shuffle()->take($targetOccupiedCount);

        $tenantCycle = 0;

        foreach ($bedsToOccupy as $bed) {
            $tenant = $this->pickTenantForBed($bed->unit->occupants, $tenants, $maleTenants, $femaleTenants, $tenantCycle);
            if (!$tenant) {
                continue;
            }

            $unitPrice = (float) $bed->unit->price;
            $term = $this->faker->numberBetween(9, 18);
            $startDate = Carbon::now()->subMonths($this->faker->numberBetween(1, 8))->startOfMonth();
            $endDate = $startDate->copy()->addMonths($term);

            Lease::factory()->create([
                'tenant_id'        => $tenant->user_id,
                'bed_id'           => $bed->bed_id,
                'status'           => 'Active',
                'term'             => $term,
                'start_date'       => $startDate->toDateString(),
                'end_date'         => $endDate->toDateString(),
                'move_in'          => $startDate->toDateString(),
                'contract_rate'    => $unitPrice,
                // Keep advance/deposit tied to full lease amount.
                'advance_amount'   => $unitPrice,
                'security_deposit' => $unitPrice,
            ]);

            $bed->update(['status' => 'Occupied']);
        }
    }

    private function pickTenantForBed(string $occupantsType, $allTenants, $maleTenants, $femaleTenants, int &$tenantCycle)
    {
        $pool = match ($occupantsType) {
            'Male' => $maleTenants,
            'Female' => $femaleTenants,
            default => $allTenants,
        };

        if ($pool->isEmpty()) {
            $pool = $allTenants;
        }

        if ($pool->isEmpty()) {
            return null;
        }

        $tenant = $pool[$tenantCycle % $pool->count()];
        $tenantCycle++;

        return $tenant;
    }
}
