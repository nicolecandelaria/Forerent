<?php

namespace App\Livewire\Layouts\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class SecurityForm extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    public function clearFields()
    {
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetValidation();
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers()->mixedCase()->symbols()
            ],
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->forceFill([
                'password' => Hash::make($this->password),
            ])->save();

            // Reset inputs
            $this->reset(['current_password', 'password', 'password_confirmation']);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Password updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Password Update Failed: ' . $e->getMessage());
            $this->addError('current_password', 'Something went wrong. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.layouts.settings.security-form');
    }
}
