<?php

namespace App\Livewire\Layouts\Properties;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class AddPropertyModal extends Component
{
    use WithNotifications;

    /** Modal visibility */
    public $isOpen = false;

    /** Unique modal instance */
    public $modalId;

    /** Editing state */
    public $editingPropertyId = null;

    /** Form fields */
    #[Validate('required|string|max:255')]
    public $buildingName = '';

    #[Validate('required|string')]
    public $address = '';

    #[Validate('required|string')]
    public $description = '';


    public function mount($modalId = null)
    {
        $this->modalId = $modalId ?? uniqid('add_property_modal_');
    }

    protected function getListeners(): array
    {
        return [
            "openAddPropertyModal_{$this->modalId}" => 'open',
            'editProperty' => 'loadPropertyForEditing',
        ];
    }


    public function open(): void
    {
        $this->resetForm();
        $this->editingPropertyId = null;
        $this->isOpen = true;
    }

    public function loadPropertyForEditing($propertyId): void
    {
        $this->resetForm();
        $property = Property::find($propertyId);

        if ($property) {
            $this->editingPropertyId = $property->property_id;
            $this->buildingName = $property->building_name;
            $this->address = $property->address;
            $this->description = $property->prop_description;
            $this->isOpen = true;
        }
    }

    public function close(): void
    {
        $this->resetForm();
        $this->resetValidation();
        $this->isOpen = false;
        $this->dispatch('propertyModalClosed');
    }

    public function next(): void
    {
        // Validate current step
        $this->validate();

        try {
            if ($this->editingPropertyId) {
                // Update existing property
                $property = Property::find($this->editingPropertyId);
                if ($property) {
                    $property->update([
                        'building_name' => $this->buildingName,
                        'address' => $this->address,
                        'prop_description' => $this->description,
                    ]);

                    // Show success toast
                    $this->notifySuccess(
                        'Property Updated Successfully!',
                        $this->buildingName . ' has been updated.'
                    );
                }
            } else {
                // Create new property
                $property = Property::create([
                    'owner_id' => Auth::id(),
                    'building_name' => $this->buildingName,
                    'address' => $this->address,
                    'prop_description' => $this->description,
                ]);

                // Show success toast
                $this->notifySuccess(
                    'Property Created Successfully!',
                    'You can now add units to ' . $this->buildingName
                );

                // Notify other components about the new property
                $this->dispatch('propertyCreated', $property->property_id);
            }

            $this->dispatch('refresh-property-list');
            $this->close();
        } catch (\Exception $e) {
            // Show error toast
            $this->notifyError(
                'Failed to Save Property',
                'An error occurred while saving the property. Please try again.'
            );
        }
    }


    private function resetForm(): void
    {
        $this->reset([
            'buildingName',
            'address',
            'description',
            'editingPropertyId',
        ]);
        $this->resetValidation();
    }


    public function render()
    {
        return view('livewire.layouts.properties.add-property-modal');
    }
}
