<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\MaintenanceLog;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $leases = Lease::query()->get();
        $now = now();

        // Seed initial move-in inflows tied to lease terms.
        foreach ($leases as $lease) {
            $moveInDate = optional($lease->move_in)->toDateString() ?? now()->toDateString();

            // One-month advance should remain based on full lease amount.
            if ((float) $lease->advance_amount > 0) {
                Transaction::create([
                    'billing_id' => null,
                    'name' => 'Advance Payment - Lease #' . $lease->lease_id,
                    'reference_number' => sprintf('ADV-%d-%s', $lease->lease_id, now()->format('YmdHisu')),
                    'transaction_type' => 'Credit',
                    'category' => 'Advance',
                    'transaction_date' => $moveInDate,
                    'amount' => (float) $lease->advance_amount,
                    'is_recurring' => false,
                ]);
            }

            if ((float) $lease->security_deposit > 0) {
                Transaction::create([
                    'billing_id' => null,
                    'name' => 'Security Deposit - Lease #' . $lease->lease_id,
                    'reference_number' => sprintf('DEP-%d-%s', $lease->lease_id, now()->format('YmdHisu')),
                    'transaction_type' => 'Credit',
                    'category' => 'Deposit',
                    'transaction_date' => $moveInDate,
                    'amount' => (float) $lease->security_deposit,
                    'is_recurring' => false,
                ]);
            }
        }

        // Ensure the current month also has non-rent revenue so the revenue chart
        // reflects all credit categories in every seeded month context.
        $hasCurrentMonthNonRentCredits = Transaction::query()
            ->where('transaction_type', 'Credit')
            ->whereIn('category', ['Advance', 'Deposit'])
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->exists();

        if (!$hasCurrentMonthNonRentCredits) {
            $candidateLeases = $leases
                ->filter(fn ($lease) => (float) $lease->advance_amount > 0 || (float) $lease->security_deposit > 0)
                ->shuffle()
                ->take(3);

            foreach ($candidateLeases as $lease) {
                $day = max(1, fake()->numberBetween(1, (int) $now->day));
                $transactionDate = $now->copy()->startOfMonth()->day($day)->toDateString();

                if ((float) $lease->advance_amount > 0) {
                    Transaction::create([
                        'billing_id' => null,
                        'name' => 'Advance Payment - Lease #' . $lease->lease_id,
                        'reference_number' => sprintf('ADV-CM-%d-%s', $lease->lease_id, now()->format('YmdHisu')),
                        'transaction_type' => 'Credit',
                        'category' => 'Advance',
                        'transaction_date' => $transactionDate,
                        'amount' => (float) $lease->advance_amount,
                        'is_recurring' => false,
                    ]);
                }

                if ((float) $lease->security_deposit > 0) {
                    Transaction::create([
                        'billing_id' => null,
                        'name' => 'Security Deposit - Lease #' . $lease->lease_id,
                        'reference_number' => sprintf('DEP-CM-%d-%s', $lease->lease_id, now()->format('YmdHisu')),
                        'transaction_type' => 'Credit',
                        'category' => 'Deposit',
                        'transaction_date' => $transactionDate,
                        'amount' => (float) $lease->security_deposit,
                        'is_recurring' => false,
                    ]);
                }
            }
        }

        // Seed expense-side cash outflows from completed maintenance logs.
        $logs = MaintenanceLog::query()->get();
        foreach ($logs as $log) {
            $cost = (float) $log->cost;
            if ($cost <= 0) {
                continue;
            }

            Transaction::create([
                'billing_id' => null,
                'name' => 'Maintenance Expense - Request #' . $log->request_id,
                'reference_number' => sprintf('MNT-%d-%s', $log->log_id, now()->format('YmdHisu')),
                'transaction_type' => 'Debit',
                'category' => 'Maintenance',
                'transaction_date' => optional($log->completion_date)->toDateString() ?? now()->toDateString(),
                'amount' => $cost,
                'is_recurring' => false,
            ]);

            // Optional vendor payout mirror to make outflow stream more realistic.
            if (fake()->boolean(35)) {
                Transaction::create([
                    'billing_id' => null,
                    'name' => 'Vendor Payment - Maintenance #' . $log->log_id,
                    'reference_number' => sprintf('VEND-%d-%s', $log->log_id, now()->format('YmdHisu')),
                    'transaction_type' => 'Debit',
                    'category' => 'Vendor Payment',
                    'transaction_date' => optional($log->completion_date)->toDateString() ?? now()->toDateString(),
                    'amount' => round($cost * fake()->randomFloat(2, 0.40, 0.80), 2),
                    'is_recurring' => false,
                ]);
            }
        }
    }
}
