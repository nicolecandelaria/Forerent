<?php

namespace App\Livewire\Layouts\Properties;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BuildingCardsSection extends Component
{
    public $properties;
    public $selectedBuilding = null;

    public $showAddButton = true;
    public $title = 'Buildings';
    public $emptyStateTitle = 'No properties found';
    public $emptyStateDescription = 'Get started by adding your first property.';
    public $addButtonEvent = 'openAddPropertyModal_property-dashboard';

    public $eventName = 'property-selected';

    public function mount(
        $properties = null,
        $showAddButton = true,
        $title = 'Buildings',
        $addButtonEvent = null,
        $eventName = 'property-selected'
    ) {
        $this->properties = $properties ?? $this->loadPropertiesByRole();
        $this->showAddButton = $showAddButton;
        $this->title = $title;
        $this->addButtonEvent = $addButtonEvent ?? 'openAddPropertyModal_property-dashboard';
        $this->eventName = $eventName;

        // 👇 Auto-select the first building
        if ($this->properties->isNotEmpty()) {
            $this->selectedBuilding = $this->properties->first()->property_id;
        }
    }

    /**
     * 🔥 Role-based property loading
     */
    protected function loadPropertiesByRole()
    {
        $user = Auth::user();

        if ($user->role === 'landlord') {
            return Property::with(['owner', 'units'])->get();
        }

        if ($user->role === 'manager') {
            // Get owner IDs from properties where this manager has assigned units
            $ownerIds = Property::whereHas('units', function ($query) use ($user) {
                $query->where('manager_id', $user->user_id);
            })->pluck('owner_id')->unique();

            // Show ALL properties belonging to those owners
            return Property::whereIn('owner_id', $ownerIds)
                ->with(['owner', 'units'])
                ->get();
        }

        return collect();
    }
    public function selectBuilding($propertyId)
    {
        $this->selectedBuilding = $propertyId;

        $this->dispatch($this->eventName, id: $propertyId);
    }

    /**
     * Refresh the property list when a new property is created elsewhere.
     */
    protected function getListeners(): array
    {
        return array_merge(parent::getListeners() ?? [], [
            'refresh-property-list' => 'refreshProperties',
            'propertyCreated' => 'handleNewProperty',
        ]);
    }

    public function refreshProperties(): void
    {
        $this->properties = $this->loadPropertiesByRole();

        // maintain existing selection if still present, otherwise pick first
        if ($this->properties->isNotEmpty()) {
            if (!$this->properties->pluck('property_id')->contains($this->selectedBuilding)) {
                $this->selectedBuilding = $this->properties->first()->property_id;
            }
        } else {
            $this->selectedBuilding = null;
        }
    }

    public function handleNewProperty($propertyId): void
    {
        // reload list then select the newly created property
        $this->refreshProperties();
        $this->selectedBuilding = $propertyId;
    }

    public function render()
    {
        return view('livewire.layouts.properties.building-cards-section');
    }
}
