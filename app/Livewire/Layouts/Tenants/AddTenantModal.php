<?php

namespace App\Livewire\Layouts\Tenants;

use App\Livewire\Concerns\WithNotifications;
use App\Notifications\NewAccount;
use App\Services\PasswordGenerator;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Bed;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddTenantModal extends Component
{
    use WithFileUploads, WithNotifications;

    public $isOpen = false;
    public $modalId;

    // --- Profile Information ---
    #[Validate('nullable|image|max:10240')]
    public $profilePicture = null;

    #[Validate('required|min:2')]
    public $firstName = '';

    #[Validate('required|min:2')]
    public $lastName = '';

    #[Validate('required')]
    public $gender = '';

    // --- Contact Information ---
    #[Validate('required|numeric|digits:10')]
    public $phoneNumber = '';

    #[Validate('required|email|unique:users,email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')]
    public $email = '';

    // --- Rent Details ---
    #[Validate('required')]
    public $selectedBuilding = '';

    #[Validate('required')]
    public $selectedUnit = '';

    #[Validate('required')]
    public $selectedBed = '';

    #[Validate('required')]
    public $dormType = '';

    #[Validate('required')]
    public $term = '';

    #[Validate('required|date')]
    public $startDate = '';

    #[Validate('required')]
    public $shift = '';

    public $autoRenew = false;

    // --- Move In Details ---
    #[Validate('required|date')]
    public $moveInDate = '';

    #[Validate('required|numeric')]
    public $monthlyRate = '';

    #[Validate('required|numeric')]
    public $securityDeposit = '';

    #[Validate('required')]
    public $paymentStatus = '';

    public $registration = '';

    // --- Dropdown Data ---
    public $buildings = [];
    public $units = [];
    public $beds = [];

    public function mount($modalId = null)
    {
        $this->modalId = $modalId ?? uniqid('add_tenant_modal_');
        $this->loadBuildings();
    }

    #[On('open-add-tenant-modal')]
    public function open()
    {
        $this->resetForm();
        $this->loadBuildings();
        $this->isOpen = true;
    }

    public function close()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->isOpen = false;
    }

    protected function loadBuildings()
    {
        // Only show properties where the logged-in manager manages at least one unit
        $this->buildings = Property::whereHas('units', function ($query) {
            $query->where('manager_id', Auth::id());
        })
            ->get(['property_id', 'building_name']);
    }

    public function updatedSelectedBuilding($propertyId)
    {
        $this->selectedUnit = '';
        $this->selectedBed = '';
        $this->units = [];
        $this->beds = [];

        if ($propertyId) {
            // Only show units the manager is assigned to
            $this->units = Unit::where('property_id', $propertyId)
                ->where('manager_id', Auth::id())
                ->get(['unit_id', 'unit_number']);
        }
    }

    public function updatedSelectedUnit($unitId)
    {
        $this->selectedBed = '';
        $this->beds = [];
        $this->dormType = '';
        $this->monthlyRate = '';

        if ($unitId) {
            $this->beds = Bed::where('unit_id', $unitId)
                ->where('status', 'Vacant')
                ->get(['bed_id', 'bed_number']);

            $unit = Unit::find($unitId);
            if ($unit) {
                $this->dormType = $unit->occupants;
                $this->monthlyRate = $unit->price; // 👈 auto-fill from unit price
            }
        }
    }
    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $photoPath = $this->profilePicture
                ? $this->profilePicture->store('profile-photos', 'public')
                : null;

            $password = PasswordGenerator::generate(); // 👈 generate once, reuse

            $user = User::create([
                'first_name'  => $this->firstName,
                'last_name'   => $this->lastName,
                'email'       => $this->email,
                'contact'     => $this->phoneNumber,
                'role'        => 'tenant',
                'password'    => Hash::make($password),
                'profile_img' => $photoPath,
            ]);

            Notification::send($user, new NewAccount($user->email, $password, $user->role));

            $months = (int) filter_var($this->term, FILTER_SANITIZE_NUMBER_INT);
            $endDate = \Carbon\Carbon::parse($this->startDate)->addMonths($months ?: 6);

            Lease::create([
                'tenant_id'        => $user->user_id,
                'bed_id'           => $this->selectedBed,
                'status'           => 'Active',
                'term'             => $this->term,
                'auto_renew'       => $this->autoRenew,
                'start_date'       => $this->startDate,
                'end_date'         => $endDate,
                'contract_rate'    => $this->monthlyRate,
                'advance_amount'   => $this->monthlyRate,
                'security_deposit' => $this->securityDeposit,
                'move_in'          => $this->moveInDate,
                'shift'            => $this->shift,
            ]);

            Bed::where('bed_id', $this->selectedBed)->update(['status' => 'occupied']);
        });

        // Show success toast notification
        $this->notifySuccess(
            'Tenant Added Successfully!',
            $this->firstName . ' ' . $this->lastName . ' has been added to ' . $this->selectedBed . '.'
        );

        $this->isOpen = false;
        $this->dispatch('refresh-tenant-list');
        $this->resetForm();
    }
    private function resetForm()
    {
        $this->reset([
            'profilePicture',
            'firstName',
            'lastName',
            'gender',
            'phoneNumber',
            'email',
            'selectedBuilding',
            'selectedUnit',
            'selectedBed',
            'dormType',
            'term',
            'startDate',
            'shift',
            'autoRenew',
            'moveInDate',
            'monthlyRate',
            'securityDeposit',
            'paymentStatus',
            'registration',
            'units',
            'beds',
        ]);
    }

    public function validateAndConfirm(): void
    {
        $this->validate();
        $this->dispatch('open-modal', 'save-tenant-confirmation');
    }

    public function render()
    {
        return view('livewire.layouts.tenants.add-tenant-modal');
    }
}
