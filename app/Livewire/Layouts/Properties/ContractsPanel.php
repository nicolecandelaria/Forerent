<?php

namespace App\Livewire\Layouts\Properties;

use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ContractsPanel extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function viewContract(int $leaseId, string $contractType = 'move-in'): void
    {
        $this->dispatch('open-landlord-contract-viewer', leaseId: $leaseId, contractType: $contractType);
    }

    #[On('signature-saved')]
    #[On('moveout-signature-saved')]
    public function refreshData(): void
    {
        // Re-render after a signature is saved
    }

    public function render()
    {
        $user = Auth::user();

        $baseQuery = Lease::whereHas('bed.unit.property', fn($q) => $q->where('owner_id', $user->user_id));

        $totalContracts = (clone $baseQuery)->count();
        $signedContracts = (clone $baseQuery)->where('contract_status', 'executed')->count();
        $pendingContracts = $totalContracts - $signedContracts;

        $query = Lease::whereHas('bed.unit.property', fn($q) => $q->where('owner_id', $user->user_id))
            ->with(['tenant', 'bed.unit.property'])
            ->latest('start_date');

        if ($this->filter === 'pending') {
            $query->where(fn($q) => $q->whereNull('contract_status')->orWhere('contract_status', '!=', 'executed'));
        } elseif ($this->filter === 'signed') {
            $query->where('contract_status', 'executed');
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('tenant', fn($tq) => $tq->where('first_name', 'ilike', "%{$search}%")->orWhere('last_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('bed.unit.property', fn($pq) => $pq->where('property_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('bed.unit', fn($uq) => $uq->where('unit_number', 'ilike', "%{$search}%"));
            });
        }

        return view('livewire.layouts.properties.contracts-panel', [
            'leases' => $query->paginate(10),
            'totalContracts' => $totalContracts,
            'signedContracts' => $signedContracts,
            'pendingContracts' => $pendingContracts,
        ]);
    }
}
