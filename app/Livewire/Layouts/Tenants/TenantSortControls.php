<?php

namespace App\Livewire\Layouts\Tenants;

use Livewire\Component;
use Livewire\Attributes\On;

class TenantSortControls extends Component
{
    public $activeTab = 'all';
    public $sortOrder = 'newest';
    public $counts = [
        'all'     => 0,
        'paid'    => 0,
        'pending' => 0,
        'overdue' => 0,
    ];

    public function setTab($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tenantTabChanged', tab: $tab);
    }

    public function setSortOrder($order): void
    {
        $this->sortOrder = $order;
        $this->dispatch('tenantSortChanged', sortOrder: $order);
    }

    #[On('tenantCountsUpdated')]
    public function updateCounts(array $counts): void
    {
        $this->counts = $counts;
    }

    public function render()
    {
        return view('livewire.layouts.tenants.tenant-sort-controls');
    }
}
