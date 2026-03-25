<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bed;
use App\Models\Lease;

class LeaseSeeder extends Seeder
{
    public function run(): void
    {
        // Get Tricia (the main test tenant) and Marcus (the main test manager)
        $tricia = User::where('role', 'tenant')->where('email', 'tenant@example.com')->first();
        $marcus = User::where('role', 'manager')->where('email', 'manager@example.com')->first();

        // Get all vacant beds with their units loaded
        $availableBeds = Bed::where('status', 'Vacant')->with('unit')->get();

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

            // Stop if no beds left
            if ($availableBeds->isEmpty()) {
                break;
            }

            // Filter beds that are in units with a manager
            $managedBeds = $availableBeds->filter(function ($bed) {
                return !is_null($bed->unit->manager_id);
            });

            // Filter beds that match tenant gender based on unit occupants
            $matchingBeds = $managedBeds->filter(function ($bed) use ($tenant) {
                $occupantsType = $bed->unit->occupants; // Male, Female, Co-ed
                return $occupantsType === 'Co-ed' || $occupantsType === $tenant->gender;
            });

            // If no matching bed, skip tenant
            if ($matchingBeds->isEmpty()) {
                continue;
            }

            // Pick a random matching bed
            $bed = $matchingBeds->random();

            // Get the unit price
            $unitPrice = $bed->unit->price;

            // Create the lease
            Lease::factory()->create([
                'tenant_id'        => $tenant->user_id,
                'bed_id'           => $bed->bed_id,
                'contract_rate'    => $unitPrice,
                'advance_amount'   => $unitPrice,
                'security_deposit' => $unitPrice,
            ]);

            // Mark bed as occupied
            $bed->update(['status' => 'Occupied']);

            // Remove the bed from available list
            $availableBeds = $availableBeds->reject(
                fn($b) => $b->bed_id === $bed->bed_id
            );
        }
    }
}
