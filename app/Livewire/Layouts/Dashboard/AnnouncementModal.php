<?php

namespace App\Livewire\Layouts\Dashboard;

use App\Models\Announcement;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use Illuminate\Support\Facades\Auth; // ← ADD THIS IMPORT
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class AnnouncementModal extends Component
{
    public $showModal = false;
    // Removed: public $showConfirmation = false; (No longer needed)

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
        if (Auth::user()->role == "landlord") // ← CHANGE TO Auth::user()
        {
            $this->properties = Property::where('owner_id', Auth::id()) // ← CHANGE TO Auth::id()
                ->orderBy('building_name')
                ->get();
        } else if (Auth::user()->role == "manager") // ← CHANGE TO Auth::user()
        {
            $propertyIds = Unit::where('manager_id', Auth::id()) // ← CHANGE TO Auth::id()
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
        // Removed: $this->showConfirmation = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->headline = '';
        $this->details = '';
        $this->propertyId = null;
        $this->resetValidation();
    }

    // This is the final action called by the <x-ui.modal-confirm> component
    public function confirmPost()
    {
        $this->validate();

        $announcement = new Announcement();

        if (Auth::user()->role === "landlord") // ← CHANGE TO Auth::user()
        {
            $announcement = $this->saveToDatabase('manager');
        } else if (Auth::user()->role === "manager") { // ← CHANGE TO Auth::user()
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
            'author_id' => Auth::id(), // ← CHANGE TO Auth::id()
            'property_id' => $this->propertyId,
            'headline' => $this->headline,
            'details' => $this->details,
            'sender_role' => Auth::user()->role, // ← CHANGE TO Auth::user()
            'recipient_role' => $recipientRole,
            'created_at' => now()
        ]);

        return $announcement;
    }

    private function sendEmailToRecipient($announcement)
    {
        $property = Property::find($this->propertyId);

        if (Auth::user()->role === "landlord") { // ← CHANGE TO Auth::user()
            $managers = $property->managers;

            Notification::send($managers, new NewAnnouncement($announcement));
        } else if (Auth::user()->role === "manager") { // ← CHANGE TO Auth::user()
            $tenants = $property->tenantsForManager(Auth::id())->get(); // ← CHANGE TO Auth::id()

            Notification::send($tenants, new NewAnnouncement($announcement));
        }
    }

    public function render()
    {
        return view('livewire.layouts.dashboard.announcement-modal');
    }
}