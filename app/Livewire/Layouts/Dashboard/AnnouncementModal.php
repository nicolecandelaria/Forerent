<?php

namespace App\Livewire\Layouts\Dashboard;
use App\Models\Announcement;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class AnnouncementModal extends Component
{
    public $showModal = false;
    public $showConfirmation = false;
    public $headline = '';
    public $details = '';
    public $propertyId = null;

    public $properties = [];

    protected $rules = [
        'headline' => 'required|min:3|max:200',
        'details' => 'required|min:10|max:1000',
        'propertyId' => 'required|exists:properties,property_id',
    ];

    protected $messages = [
        'headline.required' => 'Please enter a headline for your announcement.',
        'details.required' => 'Please enter details for your announcement.',
        'property_id.required' => 'Please select a property.',
    ];

    protected $listeners = ['open-announcement-modal' => 'openModal'];

    public function mount()
    {
        if (auth()->user()->role == "landlord")
        {
            $this->properties = Property::where('owner_id', auth()->id())
                ->orderBy('building_name')
                ->get();
        } else if (auth()->user()->role == "manager")
        {
            $propertyIds = Unit::where('manager_id', auth()->id())
                ->pluck('property_id')
                ->unique();

            $this->properties = Property::whereIn('property_id', $propertyIds)
                ->orderBy('building_name')
                ->get();
        }
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showConfirmation = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->headline = '';
        $this->details = '';
        $this->propertyId = null;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();
        $this->showConfirmation = true;
    }

    public function cancelConfirmation()
    {
        $this->showConfirmation = false;
    }

    public function confirmPost()
    {
        $this->validate();

        $announcement = new Announcement();

        if (auth()->user()->role === "landlord")
        {
            $announcement = $this->saveToDatabase('manager');
        } else if (auth()->user()->role === "manager") {
            $announcement = $this->saveToDatabase('tenant');
        }

        $this->sendEmailToRecipient($announcement);

        session()->flash('message', 'Announcement posted successfully!');

        $this->closeModal();
        $this->dispatch('announcement-posted');
    }

    private function saveToDatabase($recipientRole)
    {
        $announcement = Announcement::create([
            'author_id' => auth()->id(),
            'property_id' => $this->propertyId,
            'headline' => $this->headline,
            'details' => $this->details,
            'sender_role' => auth()->user()->role,
            'recipient_role' => $recipientRole,
            'created_at' => now()
        ]);

        return $announcement;
    }

    private function sendEmailToRecipient($announcement)
    {
        $property = Property::find($this->propertyId);

        if (auth()->user()->role === "landlord") {
            $managers = $property->managers;

            Notification::send($managers, new NewAnnouncement($announcement));
        } else if (auth()->user()->role === "manager") {
            $tenants = $property->tenantsForManager(auth()->id())->get();

            Notification::send($tenants, new NewAnnouncement($announcement));
        }
    }

    public function render()
    {
        return view('livewire.layouts.dashboard.announcement-modal');
    }
}
