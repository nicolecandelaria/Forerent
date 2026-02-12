<?php

namespace App\Livewire\Layouts\Maintenance;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\On; // Import the attribute

class AddMaintenanceModal extends Component
{
    use WithFileUploads;
    public $modalId;
    public $isOpen = false;

    // Form Fields
    public $category = 'HVAC';
    public $description = '';
    public $image = null;

    // Display Data
    public $residentName = '';
    public $unitNumber = '';
    public $buildingName = '';
    public $ticketNumber = '';
    public $reportedDate = '';

    // Hidden Data
    public $leaseId = null;

    #[On('open-maintenance-modal')]
    public function open()
    {
        $this->resetForm();
        $this->loadTenantDetails();
        $this->isOpen = true;
    }

    public function mount()
    {
        $this->modalId = 'maintenance-modal-' . uniqid();
    }

    #[On('close-modal')]
    public function closeModalHandler($modalName)
    {
        if ($modalName === 'discard-maintenance-confirmation') {
            $this->close();
        }
    }

    public function loadTenantDetails()
    {
        $user = Auth::user();
        $this->residentName = $user->name ?? $user->first_name . ' ' . $user->last_name;
        $this->reportedDate = Carbon::now()->format('F j, Y');

        $nextId = DB::table('maintenance_requests')->max('request_id') + 1;
        $this->ticketNumber = 'MR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $leaseDetails = DB::table('leases')
            ->join('beds', 'leases.bed_id', '=', 'beds.bed_id')
            ->join('units', 'beds.unit_id', '=', 'units.unit_id')
            ->join('properties', 'units.property_id', '=', 'properties.property_id')
            ->where('leases.tenant_id', $user->user_id)
            ->where('leases.status', 'Active')
            ->select(
                'leases.lease_id',
                'units.unit_number',
                'properties.building_name'
            )
            ->first();

        if ($leaseDetails) {
            $this->leaseId = $leaseDetails->lease_id;
            $this->unitNumber = 'Unit ' . $leaseDetails->unit_number;
            $this->buildingName = $leaseDetails->building_name;
        } else {
            $this->leaseId = null;
            $this->unitNumber = 'No Active Unit';
            $this->buildingName = 'N/A';
        }
    }

    public function selectCategory($name)
    {
        $this->category = $name;
    }

    public function validateAndConfirm()
    {
        $this->validate([
            'category' => 'required',
            'description' => 'required|min:10',
            'image' => 'nullable|image|max:5120',
        ]);

        if (!$this->leaseId) {
            $this->addError('description', 'You must have an active lease to submit a request.');
            return;
        }

        $this->dispatch('open-modal', 'save-maintenance-confirmation');
    }

    public function save()
    {
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('maintenance_photos', 'public');
        }

        $urgency = 'Normal';

        DB::table('maintenance_requests')->insert([
            'lease_id' => $this->leaseId,
            'ticket_number' => $this->ticketNumber,
            'problem' => $this->description,
            'status' => 'Pending',
            'urgency' => $urgency,
            'log_date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('message', 'Maintenance request submitted successfully!');

        $this->dispatch('close-modal', 'save-maintenance-confirmation');
        $this->dispatch('refresh-maintenance-list');
        $this->close();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['description', 'image', 'category', 'leaseId']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.layouts.maintenance.add-maintenance-modal');
    }
}
