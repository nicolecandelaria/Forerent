<?php

namespace App\Livewire\Layouts\Contracts;

use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ContractsOverview extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, pending, signed
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function viewContract(int $leaseId, string $contractType = 'move-in'): void
    {
        $this->dispatch('open-landlord-contract-viewer', leaseId: $leaseId, contractType: $contractType);
    }

    public function render()
    {
        $user = Auth::user();

        $query = Lease::whereHas('bed.unit.property', function ($q) use ($user) {
                $q->where('owner_id', $user->user_id);
            })
            ->with(['tenant', 'bed.unit.property'])
            ->latest('start_date');

        // Filter by contract status
        if ($this->filter === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('contract_status')
                  ->orWhere('contract_status', '!=', 'executed');
            });
        } elseif ($this->filter === 'signed') {
            $query->where('contract_status', 'executed');
        }

        // Search by tenant name or property/unit
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('tenant', function ($tq) use ($search) {
                    $tq->where('first_name', 'ilike', "%{$search}%")
                       ->orWhere('last_name', 'ilike', "%{$search}%");
                })
                ->orWhereHas('bed.unit.property', function ($pq) use ($search) {
                    $pq->where('property_name', 'ilike', "%{$search}%");
                })
                ->orWhereHas('bed.unit', function ($uq) use ($search) {
                    $uq->where('unit_number', 'ilike', "%{$search}%");
                });
            });
        }

        $leases = $query->paginate(15);

        // Summary counts
        $baseQuery = Lease::whereHas('bed.unit.property', function ($q) use ($user) {
            $q->where('owner_id', $user->user_id);
        });

        $totalContracts = (clone $baseQuery)->count();
        $signedContracts = (clone $baseQuery)->where('contract_status', 'executed')->count();
        $pendingContracts = $totalContracts - $signedContracts;

        return view('livewire.layouts.contracts.contracts-overview', [
            'leases' => $leases,
            'totalContracts' => $totalContracts,
            'signedContracts' => $signedContracts,
            'pendingContracts' => $pendingContracts,
        ]);
    }
}
