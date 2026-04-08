<?php

namespace App\Livewire\Layouts\Tenants;

use App\Models\Lease;
use App\Models\UtilityBill;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TenantUtilityHistory extends Component
{
    use WithPagination;

    public $activeTab = 'all';
    public $selectedMonth = null;
    public $selectedYear = null;
    public $expandedRow = null;

    public function toggleRow($id)
    {
        $this->expandedRow = $this->expandedRow === $id ? null : $id;
    }

    public function setTab($tab) { $this->activeTab = $tab; $this->resetPage(); }
    public function updatedActiveTab() { $this->resetPage(); }
    public function updatedSelectedMonth() { $this->resetPage(); }
    public function updatedSelectedYear() { $this->resetPage(); }

    private function getLease()
    {
        return Lease::where('tenant_id', Auth::user()->user_id)
            ->whereNull('deleted_at')
            ->with('bed.unit')
            ->latest('start_date')
            ->first();
    }

    private function baseQuery()
    {
        $lease = $this->getLease();
        $unitId = $lease?->bed?->unit?->unit_id;

        $query = UtilityBill::where('unit_id', $unitId)
            ->with('unit.property');

        // Only show utility bills from the tenant's lease start date onwards
        if ($lease?->start_date) {
            $query->where('billing_period', '>=', $lease->start_date->startOfMonth());
        }

        return $query;
    }

    public function render()
    {
        $monthOptions = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $baseQuery = $this->baseQuery();

        $counts = [
            'all'         => (clone $baseQuery)->count(),
            'electricity' => (clone $baseQuery)->where('utility_type', 'electricity')->count(),
            'water'       => (clone $baseQuery)->where('utility_type', 'water')->count(),
        ];

        $query = clone $baseQuery;

        match ($this->activeTab) {
            'electricity' => $query->where('utility_type', 'electricity'),
            'water'       => $query->where('utility_type', 'water'),
            default       => null,
        };

        if ($this->selectedMonth) {
            $query->whereMonth('billing_period', $this->selectedMonth);
        }

        if ($this->selectedYear) {
            $query->whereYear('billing_period', $this->selectedYear);
        }

        $items = $query->orderBy('billing_period', 'desc')->paginate(10);

        $currentYear = (int) date('Y');
        $yearOptions = array_combine(
            range($currentYear, $currentYear - 4),
            range($currentYear, $currentYear - 4)
        );

        return view('livewire.layouts.tenants.tenant-utility-history', [
            'items'        => $items,
            'counts'       => $counts,
            'monthOptions' => $monthOptions,
            'yearOptions'  => $yearOptions,
        ]);
    }
}
