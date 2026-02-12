<?php

namespace App\Livewire\Layouts\Financials;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentReceipts extends Component
{
    use WithPagination;

    public $activeTab = 'all';
    public $filterPeriod = '';
    public $filterBuilding = '';
    public $billingIdToMarkPaid = null; // State to hold the ID

    public function updatedActiveTab()
    {
        $this->resetPage();
    }
    public function updatedFilterPeriod()
    {
        $this->resetPage();
    }
    public function updatedFilterBuilding()
    {
        $this->resetPage();
    }

    public function confirmPayment($id)
    {
        $this->billingIdToMarkPaid = $id;
        $this->dispatch('open-modal', 'mark-as-paid-confirmation');
    }

    public function viewReceipt($billingId)
    {
        // Dispatch event to the modal component with the ID
        $this->dispatch('open-payment-receipt', billingId: $billingId);
    }

    public function markAsPaid()
    {
        if ($this->billingIdToMarkPaid) {
            DB::table('billings')->where('billing_id', $this->billingIdToMarkPaid)->update([
                'status' => 'Paid',
                'amount' => DB::raw('to_pay'),
                'updated_at' => now()
            ]);

            $this->dispatch('show-toast', ['message' => 'Payment marked as Paid!']);

            // FIX: Explicitly close the modal after success
            $this->dispatch('close-modal', 'mark-as-paid-confirmation');

            $this->billingIdToMarkPaid = null;
        }
    }

    public function render()
    {
        // ... (Keep your existing render logic exactly the same) ...
        $baseQuery = DB::table('billings')
            ->join('leases', 'billings.lease_id', '=', 'leases.lease_id')
            ->join('users', 'leases.tenant_id', '=', 'users.user_id')
            ->select('billings.*', 'users.first_name', 'users.last_name');

        $counts = [
            'all'      => (clone $baseQuery)->count(),
            'upcoming' => (clone $baseQuery)->where('billings.status', 'Unpaid')->count(),
            'paid'     => (clone $baseQuery)->where('billings.status', 'Paid')->count(),
            'unpaid'   => (clone $baseQuery)->where('billings.status', 'Overdue')->count(),
        ];

        $query = clone $baseQuery;

        match ($this->activeTab) {
            'upcoming' => $query->where('billings.status', 'Unpaid'),
            'paid'     => $query->where('billings.status', 'Paid'),
            'unpaid'   => $query->where('billings.status', 'Overdue'),
            default    => null,
        };

        if ($this->filterPeriod) $query->whereMonth('billings.billing_date', $this->filterPeriod);

        $payments = $query->orderBy('billings.billing_date', 'desc')->paginate(10);

        return view('livewire.layouts.financials.payment-receipts', [
            'payments' => $payments,
            'counts' => $counts
        ]);
    }
}
