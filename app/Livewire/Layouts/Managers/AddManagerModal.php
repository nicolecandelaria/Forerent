<?php

namespace App\Livewire\Layouts\Managers;

use App\Livewire\Forms\AddUserForm;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class AddManagerModal extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $modalId;

    #[Validate('nullable|image|max:2048')]
    public $profilePicture = null;

    public AddUserForm $userForm;

    #[Validate('nullable')]
    public $selectedBuilding = '';

    #[Validate('nullable')]
    public $selectedFloor = '';

    #[Validate('nullable')]
    public $selectedUnits = [];

    public $buildings = [];
    public $floors = [];
    public $availableUnits = [];
    public ?int $managerId = null;
    public bool $isEditing = false;

    public function mount($modalId = null)
    {
        $this->modalId = $modalId ?? uniqid('add_manager_modal_');
        $this->loadBuildings();
    }

    protected function getListeners(): array
    {
        return [
            "openManagerModal_{$this->modalId}" => 'open',
        ];
    }

    public function open($managerId = null): void
    {
        $this->resetForm();

        if ($managerId) {
            $manager = User::find($managerId);

            if ($manager) {
                $this->isEditing = true;
                $this->managerId = $manager->user_id;
                $this->userForm->setUser($manager);
                $this->loadExistingAssignments($manager->user_id);
            }
        }

        $this->isOpen = true;
    }

    public function loadBuildings()
    {
        $this->buildings = Property::where('owner_id', Auth::id())
            ->get(['property_id', 'building_name']);
    }

    public function updatedSelectedBuilding($propertyId)
    {
        $this->selectedFloor = '';
        $this->floors = [];
        $this->availableUnits = [];

        if ($propertyId) {
            $this->floors = Unit::where('property_id', $propertyId)
                ->distinct()
                ->orderBy('floor_number')
                ->pluck('floor_number')
                ->toArray();
        }
    }

    public function updatedSelectedFloor($floor)
    {
        $this->availableUnits = [];

        if ($this->selectedBuilding && $floor) {
            $this->availableUnits = $this->getUnitsForFloor($this->selectedBuilding, $floor, $this->managerId);
        }
    }

    private function loadExistingAssignments($managerId)
    {
        $firstUnit = Unit::where('manager_id', $managerId)
            ->with('property')
            ->whereHas('property', function ($q) {
                $q->where('owner_id', Auth::id());
            })
            ->first();

        if ($firstUnit) {
            $this->selectedBuilding = $firstUnit->property_id;
            $this->updatedSelectedBuilding($firstUnit->property_id);

            $this->selectedFloor = $firstUnit->floor_number;
            $this->updatedSelectedFloor($firstUnit->floor_number);

            $managedUnitIds = Unit::where('manager_id', $managerId)
                ->where('property_id', $firstUnit->property_id)
                ->where('floor_number', $firstUnit->floor_number)
                ->pluck('unit_id')
                ->toArray();

            foreach ($managedUnitIds as $id) {
                $this->selectedUnits[$id] = true;
            }
        }
    }

    private function getUnitsForFloor($propertyId, $floor, $managerId = null): array
    {
        $units = Unit::where('property_id', $propertyId)
            ->where('floor_number', $floor)
            ->whereHas('property', function ($q) {
                $q->where('owner_id', Auth::id());
            })
            ->where(function ($query) use ($managerId) {
                $query->whereNull('manager_id');
                if (!is_null($managerId)) {
                    $query->orWhere('manager_id', $managerId);
                }
            })
            ->orderBy('unit_id')
            ->get(['unit_id', 'manager_id', 'unit_number']);

        return $units->map(fn($unit) => [
            'id' => $unit->unit_id,
            'number' => "Unit {$unit->unit_number}",
            'checked' => $unit->manager_id == $managerId,
        ])->toArray();
    }

    /**
     * NEW: Checks validation BEFORE opening the confirmation modal.
     */
    public function validateAndConfirm()
    {
        $this->validate(); // Stops here if fields are missing
        $this->dispatch('open-modal', 'save-manager-confirmation');
    }

    public function save()
    {
        $this->validate();

        // 1. Save Manager (Pass 'manager' string explicitly to fix DB crash)
        $manager = $this->userForm->store('manager');

        // 2. Handle Profile Picture Upload manually
        if ($this->profilePicture && !is_string($this->profilePicture)) {
            $path = $this->profilePicture->store('profile-photos', 'public');
            $manager->update(['profile_photo_path' => $path]);
        }

        // 3. Save Assignments
        if ($this->selectedBuilding && $this->selectedFloor) {
            Unit::where('property_id', $this->selectedBuilding)
                ->where('floor_number', $this->selectedFloor)
                ->where('manager_id', $manager->user_id)
                ->whereNotIn('unit_id', array_keys(array_filter($this->selectedUnits)))
                ->update(['manager_id' => null]);

            $selectedUnitIds = array_keys(array_filter($this->selectedUnits));

            Unit::whereIn('unit_id', $selectedUnitIds)
                ->update(['manager_id' => $manager->user_id]);
        }

        $this->close();

        $this->dispatch('refresh-manager-list');
        $this->dispatch('managerUpdated', managerId: $manager->user_id);

        // Close the confirmation modal
        $this->dispatch('close-modal', 'save-manager-confirmation');

        session()->flash('message', $this->isEditing ? 'Manager updated successfully.' : 'Manager added successfully.');
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['profilePicture', 'selectedBuilding', 'selectedFloor', 'selectedUnits', 'floors', 'availableUnits', 'managerId', 'isEditing']);
        $this->userForm->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.layouts.managers.add-manager-modal');
    }
}
