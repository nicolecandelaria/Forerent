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
        $record = DB::table('billings')
            ->join('leases', 'billings.lease_id', '=', 'leases.lease_id')
            ->join('users', 'leases.tenant_id', '=', 'users.user_id')
            ->join('beds', 'leases.bed_id', '=', 'beds.bed_id')
            ->join('units', 'beds.unit_id', '=', 'units.unit_id')
            ->join('properties', 'units.property_id', '=', 'properties.property_id')
            ->where('billings.billing_id', $billingId)
            ->select('billings.*', 'users.*', 'units.unit_number', 'properties.building_name', 'properties.address')
            ->first();

        if ($record) {
            $this->data = $this->formatReceiptData($record);
            $this->isOpen = true;
        } else {
            logger("Modal Error: No record found for Billing ID " . $billingId);
        }
    }

    
    private function formatReceiptData($record)
    {
        $billingDate = \Carbon\Carbon::parse($record->billing_date);

        return [
            'invoice_no'  => '20250825-' . str_pad($record->billing_id, 3, '0', STR_PAD_LEFT),
            'issued_date' => $billingDate->format('F d, Y'),
            'due_date'    => $billingDate->copy()->addDays(20)->format('F d, Y'),

            'tenant' => [
                'name'     => $record->first_name . ' ' . $record->last_name,
                'unit'     => 'Unit ' . $record->unit_number,
                'building' => $record->building_name,
                'address'  => $record->address,
                'contact'  => $record->tenant_contact ?? 'N/A',
            ],

            'payment' => [
                'date_paid'  => $record->updated_at ? \Carbon\Carbon::parse($record->updated_at)->format('F d, Y') : 'Pending',
                'txn_id'     => 'TXN' . $billingDate->format('Ymd'),
                'lease_type' => 'Monthly',
                'period'     => $billingDate->format('F Y'),
            ],

            'recipient' => [
                'name'     => 'Leiramarie Sanbi', // Matching your reference image
                'position' => 'Property Manager',
                'contact'  => '09456530232',
            ],

            'financials' => [
                'description' => 'Unit ' . $record->unit_number . ' - Monthly Rent',
                'amount'      => $record->to_pay,
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
