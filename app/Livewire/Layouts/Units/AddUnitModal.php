<?php

namespace App\Livewire\Layouts\Units;

use Livewire\Component;
use App\Livewire\Concerns\WithNotifications;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;

class AddUnitModal extends Component
{
    use WithNotifications;

    public $isOpen = false;
    public $modalId;
    public $editingUnitId = null;

    public $currentStep = 1;
    public $steps = [
        1 => 'Unit Details',
        2 => 'Model Amenities',
        3 => 'Review & Predict',
    ];

    public $properties = [];
    public $property_id;
    public $floor_number;
    public $m_f = 'Co-ed';
    public $bed_type;
    public $room_type;
    public $room_cap;
    public $unit_cap;

    public $model_amenities = [];
    public $amenity_labels = [];

    public $predicted_price = null;
    public $actual_price;
    public $is_predicting = false;

    // Fixed the missing comma here
    protected $rules = [
        'property_id' => 'required|integer|exists:properties,property_id',
        'floor_number' => 'required|integer|min:0',
        'm_f' => 'required|in:Male,Female,Co-ed',
        'bed_type' => 'required|in:Single,Bunk,Twin',
        'room_type' => 'required|in:Standard,Deluxe,Suite',
        'room_cap' => 'required|integer|min:1',
        'unit_cap' => 'required|integer|min:1',
        'actual_price' => 'required|numeric|min:0|max:999999.99',
    ];

    public function mount($modalId = null)
    {
        $this->modalId = $modalId ?? uniqid('add_unit_modal_');
        try {
            $this->properties = Property::all(['property_id', 'building_name']);
        } catch (\Exception $e) {
            $this->properties = collect([]);
        }
        $this->initializeAmenities();
    }

    protected function getListeners(): array
    {
        return [
            "openAddUnitModal_{$this->modalId}" => 'open',
            'open-unit-modal' => 'loadUnitForEditing',
        ];
    }

    #[On('open-add-unit-modal')]
    public function open(): void
    {
        $this->resetForm();
        $this->editingUnitId = null;
        $this->isOpen = true;
    }

    #[On('open-unit-modal')]
    public function loadUnitForEditing($unitId)
    {
        $this->resetForm();
        $unit = Unit::find($unitId);

        if ($unit) {
            $this->editingUnitId = $unit->unit_id;
            $this->property_id = $unit->property_id;
            $this->floor_number = $unit->floor_number;
            $this->m_f = $unit->{'m/f'} ?? $unit->gender ?? 'Co-ed';
            $this->room_type = $unit->room_type;
            $this->bed_type = $unit->bed_type;
            $this->unit_cap = $unit->unit_cap;
            $this->room_cap = $unit->room_cap;
            $this->actual_price = $unit->price;
            $this->predicted_price = $unit->price;

            $savedAmenities = json_decode($unit->amenities, true) ?? [];
            foreach ($this->model_amenities as $key => $value) {
                if (in_array($key, $savedAmenities)) {
                    $this->model_amenities[$key] = true;
                }
            }

            $this->isOpen = true;
            $this->currentStep = 1;
        }
    }

    public function close(): void
    {
        $this->resetForm();
        $this->resetValidation();
        $this->isOpen = false;
        $this->dispatch('unitModalClosed');
    }

    private function initializeAmenities()
    {
        $amenity_keys = [
            'Fully_furnished',
            'Free_Wifi',
            'Hot_Cold_Shower',
            'Electric_Fan',
            'Water_Kettle',
            'Closet_Cabinet',
            'Housekeeping',
            'Refrigerator',
            'Microwave',
            'Rice_Cooker',
            'Dining_Table',
            'Utility_Subsidy',
            'AC_Unit',
            'Induction_Cooker',
            'Washing_Machine',
            'Access_Pool',
            'Access_Gym',
            'Bunk_Bed_Mattress'
        ];

        foreach ($amenity_keys as $key) {
            $this->amenity_labels[$key] = ucwords(str_replace('_', ' ', $key));
        }
        $this->model_amenities = array_fill_keys($amenity_keys, false);
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'property_id' => 'required',
                'floor_number' => 'required|numeric',
                'room_type' => 'required',
                'unit_cap' => 'required|numeric',
                'room_cap' => 'required|numeric',
            ]);
        }

        if ($this->currentStep == 2) {
            $this->runPrediction();
        }

        if ($this->currentStep < count($this->steps)) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    private function runPrediction()
    {
        $this->is_predicting = true;
        $dataForModel = [
            'Floor' => (int) $this->floor_number,
            'M/F' => $this->m_f,
            'Bed type' => $this->bed_type,
            'Room type' => $this->room_type,
            'Room capacity' => (int) $this->room_cap,
            'Unit capacity' => (int) $this->unit_cap,
        ];
        $dataForModel = array_merge($dataForModel, $this->model_amenities);

        try {
            $response = Http::timeout(5)->post('http://price_api:8000/predict', $dataForModel);
            if ($response->successful()) {
                $this->predicted_price = $response->json('predicted_price');
            } else {
                $this->predicted_price = rand(8000, 12000);
            }
        } catch (\Exception $e) {
            $this->predicted_price = rand(5000, 15000);
        }

        if (!$this->editingUnitId) {
            $this->actual_price = $this->predicted_price;
        }
        $this->is_predicting = false;
    }

    public function saveUnit()
    {
        $this->validate();

        try {
            if (auth()->user()->role === 'manager' && !$this->editingUnitId) {
                $this->notifyError(
                    'Authorization Failed',
                    'Managers are not authorized to create new units.'
                );
                return;
            }

            $checkedAmenities = array_keys(array_filter($this->model_amenities));

            $data = [
                'property_id' => $this->property_id,
                'floor_number' => $this->floor_number,
                'm/f' => $this->m_f,
                'bed_type' => $this->bed_type,
                'room_type' => $this->room_type,
                'room_cap' => $this->room_cap,
                'unit_cap' => $this->unit_cap,
                'price' => $this->actual_price,
                'amenities' => json_encode($checkedAmenities),
            ];

            if ($this->editingUnitId) {
                $unit = Unit::find($this->editingUnitId);
                if ($unit) {
                    $unit->update($data);
                    $this->notifySuccess(
                        'Unit #' . $unit->unit_id . ' Updated Successfully!',
                        'Unit details have been updated.'
                    );
                }
            } else {
                $newUnit = Unit::create(array_merge($data, [
                    'unit_number' => $this->generateUniqueUnitNumber($this->property_id, $this->floor_number)
                ]));
                $this->notifySuccess(
                    'Unit #' . $newUnit->unit_id . ' Created Successfully!',
                    'New unit has been added to your property.'
                );
            }

            $this->close();
            $this->dispatch('refresh-unit-list');
        } catch (\Exception $e) {
            $this->notifyError(
                'Failed to Save Unit',
                'An error occurred while saving the unit. Please try again.'
            );
        }
    }

    private function generateUniqueUnitNumber($propertyId, $floorNumber): string
    {
        $baseNumber = sprintf("F%dU%d", $floorNumber, rand(100, 999));
        while (Unit::where('property_id', $propertyId)->where('unit_number', $baseNumber)->exists()) {
            $baseNumber = sprintf("F%dU%d", $floorNumber, rand(1000, 9999));
        }
        return $baseNumber;
    }

    private function resetForm(): void
    {
        $this->reset([
            'currentStep',
            'property_id',
            'floor_number',
            'm_f',
            'bed_type',
            'room_type',
            'room_cap',
            'unit_cap',
            'predicted_price',
            'actual_price',
            'is_predicting',
            'editingUnitId'
        ]);
        $this->initializeAmenities();
        $this->m_f = 'Co-ed';
    }

    public function render()
    {
        return view('livewire.layouts.units.add-unit-modal');
    }
}
