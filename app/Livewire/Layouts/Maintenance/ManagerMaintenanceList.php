<?php

namespace App\Livewire\Layouts\Maintenance;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerMaintenanceList extends Component
{
    // CHANGED: tabs are now all/pending/ongoing/completed (was all/open/pending/closed)
    public $activeTab = 'all';
    public $activeRequestId = null;

    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->activeRequestId = null;
    }

    public function selectRequest($id)
    {
        $this->activeRequestId = $id;
        $this->dispatch('managerMaintenanceSelected', requestId: $id);
    }

    public function render()
    {
        $managerId = Auth::id();

        // Base query — joins through lease → bed → unit to find tickets for this manager's units
        $baseQuery = DB::table('maintenance_requests')
            ->join('leases', 'maintenance_requests.lease_id', '=', 'leases.lease_id')
            ->join('beds', 'leases.bed_id', '=', 'beds.bed_id')
            ->join('units', 'beds.unit_id', '=', 'units.unit_id')
            ->join('users', 'leases.tenant_id', '=', 'users.user_id')
            ->where('units.manager_id', $managerId)
            ->select(
                'maintenance_requests.request_id',
                'maintenance_requests.status',
                'maintenance_requests.category',
                'maintenance_requests.ticket_number',
                'maintenance_requests.created_at',
                'units.unit_number',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as tenant_name")
            );

        // Tab counts
        $allCount       = (clone $baseQuery)->count();
        $pendingCount   = (clone $baseQuery)->where('maintenance_requests.status', 'Pending')->count();
        $ongoingCount   = (clone $baseQuery)->where('maintenance_requests.status', 'Ongoing')->count();
        $completedCount = (clone $baseQuery)->where('maintenance_requests.status', 'Completed')->count();

        // Apply tab filter
        $listQuery = clone $baseQuery;
        switch ($this->activeTab) {
            case 'pending':
                $listQuery->where('maintenance_requests.status', 'Pending');
                break;
            case 'ongoing':
                $listQuery->where('maintenance_requests.status', 'Ongoing');
                break;
            case 'completed':
                $listQuery->where('maintenance_requests.status', 'Completed');
                break;
                // 'all' — no extra filter
        }

        return view('livewire.layouts.maintenance.manager-maintenance-list', [
            'requests' => $listQuery->orderBy('maintenance_requests.created_at', 'desc')->get(),
            'counts' => [
                'all'       => $allCount,
                'pending'   => $pendingCount,
                'ongoing'   => $ongoingCount,
                'completed' => $completedCount,
            ],
        ]);
    }
}
