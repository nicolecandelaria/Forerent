<?php

namespace App\Livewire\Layouts\Tenants;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class TenantNavigation extends Component
{
    public $tenants = [];
    public $user;
    public $activeTenantId = null;
    public ?int $selectedBuildingId = null;
    public $activeTab = 'all';
    public $sortOrder = 'newest';

    public function mount($tenants = null): void
    {
        $this->user = Auth::user();

        $firstProperty = \App\Models\Property::whereHas('units', function ($query) {
            $query->where('manager_id', Auth::id());
        })
            ->first();

        if ($firstProperty) {
            $this->selectedBuildingId = $firstProperty->property_id;
            $this->tenants = $this->loadTenants();
            $this->emitCounts();
        }
    }

    #[On('tenant-property-selected')]
    public function onBuildingSelected(int $id): void
    {
        $this->selectedBuildingId = $id;
        $this->activeTab = 'all';
        $this->tenants = $this->loadTenants();
        $this->emitCounts();
    }

    #[On('tenantTabChanged')]
    public function onTabChanged($tab): void
    {
        $this->activeTab = $tab;
        $this->activeTenantId = null;
        $this->tenants = $this->loadTenants();
        $this->emitCounts();
    }

    #[On('tenantSortChanged')]
    public function onSortChanged($sortOrder): void
    {
        $this->sortOrder = $sortOrder;
        $this->tenants = $this->loadTenants();
    }

    #[On('refresh-tenant-list')]
    public function refreshTenantList(): void
    {
        if ($this->selectedBuildingId) {
            $this->tenants = $this->loadTenants();
            $this->emitCounts();
        } else {
            $this->tenants = [];
        }
    }

    #[On('tenantActivated')]
    public function activateTenant(int $tenantId): void
    {
        $this->activeTenantId = $tenantId;
    }

    public function selectTenant(int $tenantId): void
    {
        $this->activeTenantId = $tenantId;
        $this->dispatch('tenantSelected', tenantId: $tenantId);
    }

    public function loadTenants(): array
    {
        $query = Unit::where('manager_id', Auth::id());

        if ($this->selectedBuildingId) {
            $query->where('property_id', $this->selectedBuildingId);
        }

        $tenants = $query
            ->with([
                'beds.leases' => fn($q) => $q->where('status', 'Active')->with([
                    'tenant' => fn($q) => $q->where('role', 'tenant'),
                    'billings' => fn($q) => $q->latest()->limit(1)
                ])
            ])
            ->get()
            ->flatMap(function ($unit) {
                return $unit->beds->flatMap(function ($bed) use ($unit) {
                    return $bed->leases
                        ->filter(fn($lease) => $lease->tenant !== null)
                        ->map(fn($lease) => [
                            'id'             => $lease->tenant->user_id,
                            'first_name'     => $lease->tenant->first_name,
                            'last_name'      => $lease->tenant->last_name,
                            'unit'           => $unit->unit_number,
                            'bed_number'     => $bed->bed_number,
                            'payment_status' => $lease->billings->first()?->status ?? 'No billing',
                            'next_billing'   => $lease->billings->first()?->next_billing ?? null,
                            'created_at'     => $lease->created_at,
                        ]);
                });
            })
            ->unique('id')
            ->values()
            ->toArray();

        // Apply tab filter
        $filtered = array_filter($tenants, function ($tenant) {
            return match ($this->activeTab) {
                'paid'    => $tenant['payment_status'] === 'Paid',
                'pending' => $tenant['payment_status'] === 'Pending',
                'overdue' => $tenant['payment_status'] === 'Overdue',
                default   => true, // 'all'
            };
        });

        // Apply sorting
        $direction = $this->sortOrder === 'newest' ? -1 : 1;
        usort($filtered, function ($a, $b) use ($direction) {
            $aDate = strtotime($a['next_billing'] ?? $a['created_at'] ?? '1970-01-01');
            $bDate = strtotime($b['next_billing'] ?? $b['created_at'] ?? '1970-01-01');
            return ($aDate <=> $bDate) * $direction;
        });

        return array_values($filtered);
    }

    private function emitCounts(): void
    {
        $allTenants = $this->getUnfilteredTenants();
        $counts = [
            'all'     => count($allTenants),
            'paid'    => count(array_filter($allTenants, fn($t) => $t['payment_status'] === 'Paid')),
            'pending' => count(array_filter($allTenants, fn($t) => $t['payment_status'] === 'Pending')),
            'overdue' => count(array_filter($allTenants, fn($t) => $t['payment_status'] === 'Overdue')),
        ];
        $this->dispatch('tenantCountsUpdated', counts: $counts);
    }

    private function getUnfilteredTenants(): array
    {
        $query = Unit::where('manager_id', Auth::id());

        if ($this->selectedBuildingId) {
            $query->where('property_id', $this->selectedBuildingId);
        }

        return $query
            ->with([
                'beds.leases' => fn($q) => $q->where('status', 'Active')->with([
                    'tenant' => fn($q) => $q->where('role', 'tenant'),
                    'billings' => fn($q) => $q->latest()->limit(1)
                ])
            ])
            ->get()
            ->flatMap(function ($unit) {
                return $unit->beds->flatMap(function ($bed) use ($unit) {
                    return $bed->leases
                        ->filter(fn($lease) => $lease->tenant !== null)
                        ->map(fn($lease) => [
                            'id'             => $lease->tenant->user_id,
                            'first_name'     => $lease->tenant->first_name,
                            'last_name'      => $lease->tenant->last_name,
                            'unit'           => $unit->unit_number,
                            'bed_number'     => $bed->bed_number,
                            'payment_status' => $lease->billings->first()?->status ?? 'No billing',
                            'next_billing'   => $lease->billings->first()?->next_billing ?? null,
                            'created_at'     => $lease->created_at,
                        ]);
                });
            })
            ->unique('id')
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.layouts.tenants.tenant-navigation');
    }
}
