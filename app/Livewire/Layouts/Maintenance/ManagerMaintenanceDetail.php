<?php

namespace App\Livewire\Layouts\Maintenance;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class ManagerMaintenanceDetail extends Component
{
    public $ticket          = null;
    public $ticketIdDisplay = '';
    public $successMessage  = '';
    public $feedback        = null; // tenant's feedback for this ticket

    #[On('managerMaintenanceSelected')]
    public function loadRequest($requestId)
    {
        if ($requestId === null) {
            $this->ticket   = null;
            $this->feedback = null;
            return;
        }

        $this->ticket = DB::table('maintenance_requests')
            ->where('request_id', $requestId)
            ->first();

        if ($this->ticket) {
            $this->ticketIdDisplay = $this->ticket->ticket_number
                ?? 'TKT-' . str_pad($this->ticket->request_id, 4, '0', STR_PAD_LEFT);

            // Load tenant feedback for this ticket (if submitted)
            $this->feedback = DB::table('maintenance_feedback')
                ->where('request_id', $this->ticket->request_id)
                ->first();
        }

        $this->successMessage = '';
    }

    /**
     * Manager marks the request as Ongoing (In Progress).
     * This updates the DB status — tenant sees it immediately on their next load.
     */
    public function markAsOngoing()
    {
        if (!$this->ticket) return;

        DB::table('maintenance_requests')
            ->where('request_id', $this->ticket->request_id)
            ->update([
                'status'     => 'Ongoing',
                'updated_at' => now(),
            ]);

        // Reload ticket so the view reflects the new status
        $this->ticket = DB::table('maintenance_requests')
            ->where('request_id', $this->ticket->request_id)
            ->first();

        $this->successMessage = 'Status updated to Ongoing.';

        // Tell both list components to refresh
        $this->dispatch('refresh-maintenance-list');
        $this->dispatch('refreshDashboard');
    }

    /**
     * Manager marks the request as Completed.
     */
    public function markAsCompleted()
    {
        if (!$this->ticket) return;

        DB::table('maintenance_requests')
            ->where('request_id', $this->ticket->request_id)
            ->update([
                'status'     => 'Completed',
                'updated_at' => now(),
            ]);

        $this->ticket = DB::table('maintenance_requests')
            ->where('request_id', $this->ticket->request_id)
            ->first();

        $this->feedback = DB::table('maintenance_feedback')
            ->where('request_id', $this->ticket->request_id)
            ->first();

        $this->successMessage = 'Request marked as Completed.';

        $this->dispatch('refresh-maintenance-list');
        $this->dispatch('refreshDashboard');
    }

    public function render()
    {
        return view('livewire.layouts.maintenance.manager-maintenance-detail');
    }
}
