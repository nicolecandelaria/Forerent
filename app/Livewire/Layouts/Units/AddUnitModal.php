<?php

namespace App\Livewire\Layouts\Units;

use Livewire\Component;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On; // Step 1: Ensure this is imported

class AddUnitModal extends Component
{
    public $isOpen = false;
    public $modalId;

    // All existing AddUnit properties and methods remain the same
    // --- Navigation Properties ---
    public $currentStep = 1;
    public $steps = [
        1 => 'Unit Details',
        2 => 'Model Amenities',
        3 => 'Review & Predict',
    ];

    // --- Step 1 Properties ---
    public $properties = [];
    public $property_id;
    public $floor_number;
    public $m_f = 'Co-ed';
    public $bed_type;
    public $bed_number;
    public $utility_subsidy = false;
    public $unit_capacity;
    public $room_capacity;
    public $room_type;
    public $room_cap;
    public $unit_cap;

    // --- Step 2 Properties ---
    public $model_amenities = [];
    public $amenity_labels = [];

    // All other properties from AddUnit...
    // Grouped Amenities
    public $amenities_features = [
        'ac_unit' => false,
        'hot_cold_shower' => false,
        'free_wifi' => false,
        'fully_furnished' => false,
    ];
    public $bedroom_bedding = [
        'bunk_bed_mattress' => false,
        'closet_cabinet' => false,
    ];
    public $kitchen_dining = [
        'refrigerator' => false,
        'microwave' => false,
        'water_kettle' => false,
        'rice_cooker' => false,
        'dining_table' => false,
        'induction_cooker' => false,
    ];
    public $entertainment = [];
    public $additional_items = [
        'electric_fan' => false,
        'washing_machine' => false,
    ];
    public $consumables_provided = [];
    public $property_amenities = [
        'access_pool' => false,
        'access_gym' => false,
        'housekeeping' => false,
    ];

    // --- Step 3 Properties ---
    public $predicted_price = null;
    public $actual_price;
    public $is_predicting = false;

    // --- Validation Rules ---
    protected $step1Rules = [
        'property_id' => 'required|integer|exists:properties,property_id',
        'floor_number' => 'required|integer|min:0',
        'm_f' => 'required|in:Male,Female,Co-ed',
        'bed_type' => 'required|in:Single,Bunk,Twin',
        'room_type' => 'required|in:Standard,Deluxe,Suite',
        'room_cap' => 'required|integer|min:1',
        'unit_cap' => 'required|integer|min:1',
    ];

    /*----------------------------------
    | LIFECYCLE
    ----------------------------------*/
    public function mount($modalId = null)
    {
        $this->modalId = $modalId ?? uniqid('add_unit_modal_');

        try {
            $this->properties = Property::all(['property_id', 'building_name']);
        } catch (\Exception $e) {
            // Fallback for demo/testing if database fails
            $this->properties = collect([
                (object)['property_id' => 1, 'building_name' => 'Demo Property (Please Migrate)']
            ]);
        }
        $this->initializeAmenities();
    }

    protected function getListeners(): array
    {
        return [
            "openAddUnitModal_{$this->modalId}" => 'open',
        ];
    }

    /*----------------------------------
    | UI ACTIONS
    ----------------------------------*/
    /*public function open(): void
    {
        $this->resetForm();
    }*/
    /*----------------------------------
    | UI ACTIONS
    ----------------------------------*/

    // Step 2: The Listener Attribute
    // This tells Livewire: "When you hear 'open-add-unit-modal', run this function."
    #[On('open-add-unit-modal')]
    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->resetForm();
        $this->resetValidation();
        $this->isOpen = false;
        $this->dispatch('unitModalClosed');
    }

    // All existing methods remain exactly the same...
    /*----------------------------------
    | STEPPER LOGIC & PREDICTION
    ----------------------------------*/

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
        $labels = [];
        foreach ($amenity_keys as $key) {
            $labels[$key] = ucwords(str_replace('_', ' ', $key));
        }
        $this->amenity_labels = $labels;
        $this->model_amenities = array_fill_keys($amenity_keys, false);
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
        // Merge amenities into the dataset
        $dataForModel = array_merge($dataForModel, $this->model_amenities);

        try {
            $response = Http::post('http://price_api:8000/predict', $dataForModel);

            if ($response->successful()) {
                session()->flash('success', 'Prediction model success.');
                $this->predicted_price = $response->json('predicted_price');
            } else {
                session()->flash('error', 'Prediction model returned an error.');
                $this->predicted_price = 0;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Prediction service is offline. Using estimate.');
            $this->predicted_price = rand(5000, 15000);
        }

        $this->actual_price = $this->predicted_price;
        $this->is_predicting = false;
    }

    public function masterSelectAll($checked)
    {
        $checked = (bool)$checked;
        $this->selectAll('amenities_features', $checked);
        $this->selectAll('bedroom_bedding', $checked);
        $this->selectAll('kitchen_dining', $checked);
        $this->selectAll('entertainment', $checked);
        $this->selectAll('additional_items', $checked);
        $this->selectAll('consumables_provided', $checked);
        $this->selectAll('property_amenities', $checked);
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate($this->step1Rules);
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
        $this->predicted_price = null;
        $this->actual_price = null;

        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
    public function saveUnit()
    {
        $this->validate(array_merge($this->step1Rules, [
            'actual_price' => 'required|numeric|min:0|max:999999.99'
        ]));

        if (is_null($this->predicted_price)) {
            session()->flash('error', 'Price prediction is missing.');
            return;
        }

        $checkedAmenityNames = array_keys(array_filter($this->model_amenities));

        try {
            Unit::create([
                'property_id' => $this->property_id,
                'unit_number' => $this->generateUniqueUnitNumber($this->property_id, $this->floor_number), // ← Auto-generate
                'floor_number' => $this->floor_number,
                'm/f' => $this->m_f,
                'bed_type' => $this->bed_type,
                'room_type' => $this->room_type,
                'room_cap' => $this->room_cap,
                'unit_cap' => $this->unit_cap,
                'price' => $this->actual_price,
                'amenities' => json_encode($checkedAmenityNames),
            ]);

            // ... rest of success handling
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving unit: ' . $e->getMessage());
        }
    }

    private function generateUniqueUnitNumber($propertyId, $floorNumber): string
    {
        $baseNumber = sprintf("F%dU%d", $floorNumber, rand(100, 999)); // e.g., F2U456

        while (Unit::where('property_id', $propertyId)->where('unit_number', $baseNumber)->exists()) {
            $baseNumber = sprintf("F%dU%d", $floorNumber, rand(1000, 9999));
        }

        return $baseNumber;
    }

    /*----------------------------------
    | HELPER METHODS
    ----------------------------------*/
    private function resetForm(): void
    {
        $this->reset([
            'currentStep',
            'property_id',
            'floor_number',
            'm_f',
            'bed_type',
            'bed_number',
            'utility_subsidy',
            'unit_capacity',
            'room_capacity',
            'room_type',
            'room_cap',
            'unit_cap',
            'model_amenities',
            'predicted_price',
            'actual_price',
            'is_predicting',
            'amenities_features',
            'bedroom_bedding',
            'kitchen_dining',
            'entertainment',
            'additional_items',
            'consumables_provided',
            'property_amenities',
        ]);

        // Re-initialize amenities
        $this->initializeAmenities();
        $this->m_f = 'Co-ed';
    }

    public function selectAll($group, $checked)
    {
        foreach ($this->$group as $key => $value) {
            $this->$group[$key] = $checked;
        }
    }

    /*----------------------------------
    | RENDER
    ----------------------------------*/
    public function render()
    {
        $labels = [
            'amenities_features' => [
                'ac_unit' => 'AC Unit',
                'hot_cold_shower' => 'Hot Cold Shower',
                'free_wifi' => 'Free Wifi',
                'fully_furnished' => 'Fully Furnished',
            ],
            'bedroom_bedding' => [
                'bunk_bed_mattress' => 'Bunk Bed Mattress',
                'closet_cabinet' => 'Closet Cabinet',
            ],
            'kitchen_dining' => [
                'refrigerator' => 'Refrigerator',
                'microwave' => 'Microwave',
                'water_kettle' => 'Water Kettle',
                'rice_cooker' => 'Rice Cooker',
                'dining_table' => 'Dining Table',
                'induction_cooker' => 'Induction Cooker',
            ],
            'entertainment' => [],
            'additional_items' => [
                'electric_fan' => 'Electric Fan',
                'washing_machine' => 'Washing Machine',
            ],
            'consumables_provided' => [],
            'property_amenities' => [
                'access_pool' => 'Access Pool',
                'access_gym' => 'Access Gym',
                'housekeeping' => 'Housekeeping',
            ],
        ];

        return view('livewire.layouts.units.add-unit-modal', [
            'labels' => $labels
        ]);
    }
}
