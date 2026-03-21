<?php

use App\Models\Billing;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('billings:backfill-credit-transactions {--dry-run : Show what would be created without writing data}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $this->info('Scanning paid billings for missing credit transactions...');
    if ($dryRun) {
        $this->comment('Dry run mode enabled. No data will be written.');
    }

    $scanned = 0;
    $missing = 0;
    $created = 0;
    $skippedNoAmount = 0;

    Billing::query()
        ->where('status', 'Paid')
        ->orderBy('billing_id')
        ->chunkById(200, function ($billings) use (&$scanned, &$missing, &$created, &$skippedNoAmount, $dryRun) {
            foreach ($billings as $billing) {
                $scanned++;

                $hasCredit = $billing->transactions()
                    ->where('transaction_type', 'Credit')
                    ->where('category', 'Rent Payment')
                    ->exists();

                if ($hasCredit) {
                    continue;
                }

                $missing++;

                $amount = (float) ($billing->amount ?? 0);
                if ($amount <= 0) {
                    $amount = (float) ($billing->to_pay ?? 0);
                }

                if ($amount <= 0) {
                    $skippedNoAmount++;
                    continue;
                }

                if (!$dryRun) {
                    $billing->ensureCreditTransaction();
                    $created++;
                }
            }
        }, 'billing_id', 'billing_id');

    $this->newLine();
    $this->line("Paid billings scanned: {$scanned}");
    $this->line("Missing credit transactions: {$missing}");
    if ($dryRun) {
        $this->line('Would be created: ' . ($missing - $skippedNoAmount));
    } else {
        $this->line("Created: {$created}");
    }
    $this->line("Skipped (no billable amount): {$skippedNoAmount}");

    if ($dryRun) {
        $this->comment('Run without --dry-run to apply changes.');
    } else {
        $this->info('Backfill completed.');
    }
})->purpose('Backfill missing credit transactions for already-paid billings');

Artisan::command('billings:realign-credit-transaction-dates {--dry-run : Show affected rows without writing data}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $this->info('Checking rent-payment credit transactions against billing dates...');
    if ($dryRun) {
        $this->comment('Dry run mode enabled. No data will be written.');
    }

    $query = \App\Models\Transaction::query()
        ->join('billings', 'billings.billing_id', '=', 'transactions.billing_id')
        ->where('transactions.transaction_type', 'Credit')
        ->where('transactions.category', 'Rent Payment')
        ->whereNotNull('billings.billing_date')
        ->whereColumn('transactions.transaction_date', '!=', 'billings.billing_date');

    $affected = (clone $query)->count();
    $this->line("Rows needing realignment: {$affected}");

    if (!$dryRun && $affected > 0) {
        $updated = $query->update([
            'transactions.transaction_date' => \Illuminate\Support\Facades\DB::raw('billings.billing_date'),
        ]);

        $this->line("Rows updated: {$updated}");
        $this->info('Date realignment completed.');
    }

    if ($dryRun) {
        $this->comment('Run without --dry-run to apply changes.');
    }
})->purpose('Realign rent-payment credit transaction dates to billing dates');
