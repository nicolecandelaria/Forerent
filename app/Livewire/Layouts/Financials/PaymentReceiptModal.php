<?php

namespace App\Livewire\Layouts\Financials;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentReceiptModal extends Component
{
    public $isOpen = false;
    public $data = [];

    #[On('open-payment-receipt')]
    public function open($billingId)
    {
        $this->isOpen = true;

        // 1. FETCH REAL DATA FROM DB
        // We join: Billing -> Lease -> User(Tenant) -> Bed -> Unit -> Property
        $record = DB::table('billings')
            ->join('leases', 'billings.lease_id', '=', 'leases.lease_id')
            ->join('users', 'leases.tenant_id', '=', 'users.user_id')
            ->join('beds', 'leases.bed_id', '=', 'beds.bed_id')
            ->join('units', 'beds.unit_id', '=', 'units.unit_id')
            ->join('properties', 'units.property_id', '=', 'properties.property_id')
            ->where('billings.billing_id', $billingId)
            ->select(
                'billings.*',
                'users.first_name',
                'users.last_name',
                'users.phone_number as tenant_contact',
                'units.unit_number',
                'properties.building_name',
                'properties.location',
                'leases.contract_rate'
            )
            ->first();

        if (!$record) {
            return; // Handle error if record not found
        }

        // 2. FORMAT DATA FOR THE VIEW
        $billingDate = Carbon::parse($record->billing_date);
        $transactionId = 'FT-' . (202300 + $record->billing_id); // Matching your table logic

        $this->data = [
            'invoice_no' => $transactionId,
            'issued_date' => $billingDate->format('F d, Y'),
            'due_date' => $billingDate->copy()->addDays(5)->format('F d, Y'), // Assumption: Due 5 days later

            'tenant' => [
                'name' => $record->first_name . ' ' . $record->last_name,
                'unit' => 'Unit ' . $record->unit_number,
                'building' => $record->building_name,
                'address' => $record->location,
                'contact' => $record->tenant_contact ?? 'N/A',
            ],

            'payment' => [
                'date_paid' => $record->updated_at ? Carbon::parse($record->updated_at)->format('F d, Y') : 'Pending',
                'txn_id' => $transactionId,
                'lease_type' => 'Monthly Rent',
                'period' => $billingDate->format('F Y'),
            ],

            // Placeholder for Manager (since it's not strictly linked in the seeders yet)
            'recipient' => [
                'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name ?? 'Admin',
                'position' => 'Property Manager',
                'contact' => '09123456789',
            ],

            'financials' => [
                'description' => 'Monthly Rent - ' . $billingDate->format('F'),
                'amount' => $record->to_pay,
            ]
        ];
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.layouts.financials.payment-receipt-modal');
    }
}
