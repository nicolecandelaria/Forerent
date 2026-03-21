<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Lease;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leases = Lease::where('status', 'Active')->get();

        foreach ($leases as $lease) {
            $contractPrice = (float) $lease->contract_rate;
            $startMonth = Carbon::parse($lease->move_in)->startOfMonth();
            $endMonth = Carbon::now()->startOfMonth();

            if ($startMonth->gt($endMonth)) {
                continue;
            }

            $billingDate = $startMonth->copy();

            while ($billingDate->lte($endMonth)) {
                $nextBilling = (clone $billingDate)->addMonth();
                $status = $this->resolveStatus($billingDate);

                Billing::factory()->create([
                    'lease_id'     => $lease->lease_id,
                    'billing_date' => $billingDate->toDateString(),
                    'next_billing' => $nextBilling->toDateString(),
                    'to_pay'       => $contractPrice,
                    'amount'       => $contractPrice,
                    'status'       => $status,
                ]);

                $billingDate->addMonth();
            }
        }
    }

    private function resolveStatus(Carbon $billingDate): string
    {
        $now = Carbon::now();

        if ($billingDate->lt($now->copy()->subMonths(2)->startOfMonth())) {
            return fake()->randomElement(['Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Overdue']);
        }

        if ($billingDate->lt($now->startOfMonth())) {
            return fake()->randomElement(['Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Paid', 'Overdue', 'Unpaid']);
        }

        return fake()->randomElement(['Paid', 'Paid', 'Unpaid', 'Overdue']);
    }
}
