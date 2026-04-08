<?php

namespace App\Livewire\Actions;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Notification as NotificationModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsForm extends Component
{
    use WithNotifications, WithFileUploads;

    // Form properties
    public string $firstName = '';
    public string $lastName = '';
    public string $phoneNumber = '';
    public string $email = '';

    // Government ID properties
    public string $governmentIdType = '';
    public string $governmentIdNumber = '';

    // Image upload properties
    #[Validate('nullable|image|max:10240')]
    public $profilePicture;

    #[Validate('nullable|image|max:10240')]
    public $governmentIdImage;

    // Existing image paths from DB
    public ?string $existingProfileImg = null;
    public ?string $existingGovernmentIdImage = null;

    // Snapshot of loaded values for dirty-checking
    public string $originalFirstName = '';
    public string $originalLastName = '';
    public string $originalPhoneNumber = '';
    public string $originalEmail = '';
    public ?string $originalProfileImg = null;
    public ?string $originalGovernmentIdImage = null;
    public string $originalGovernmentIdType = '';
    public string $originalGovernmentIdNumber = '';

    public bool $hasPendingChanges = false;

    public function mount(): void
    {
        $this->loadUserData();
    }

    public function hydrate(): void
    {
        if ($this->hasPendingChanges || !Auth::check()) {
            return;
        }

        $user = $this->resolveCurrentUser();

        if (!$user) {
            return;
        }

        $shouldReload = ($this->firstName === '' && (string) ($user->first_name ?? '') !== '')
            || ($this->lastName === '' && (string) ($user->last_name ?? '') !== '')
            || ($this->email === '' && (string) ($user->email ?? '') !== '')
            || ($this->phoneNumber === '' && $this->normalizePhone((string) ($user->contact ?? '')) !== '');

        if ($shouldReload) {
            $this->loadUserData($user);
        }
    }

    public function updatedPhoneNumber($value): void
    {
        $this->phoneNumber = $this->normalizePhone((string) $value);
        $this->recomputePendingChanges();
    }

    public function updated($property): void
    {
        if (in_array($property, [
            'firstName',
            'lastName',
            'email',
            'profilePicture',
            'governmentIdImage',
            'governmentIdType',
            'governmentIdNumber',
            'existingProfileImg',
            'existingGovernmentIdImage',
        ], true)) {
            $this->recomputePendingChanges();
        }
    }

    private function loadUserData(?User $user = null): void
    {
        $user ??= $this->resolveCurrentUser();

        if (!$user) {
            $this->firstName = '';
            $this->lastName = '';
            $this->phoneNumber = '';
            $this->email = '';
            $this->existingProfileImg = null;
            $this->existingGovernmentIdImage = null;
            $this->profilePicture = null;
            $this->governmentIdImage = null;
            $this->governmentIdType = '';
            $this->governmentIdNumber = '';

            $this->syncOriginalState();
            $this->hasPendingChanges = false;

            return;
        }

        $this->email = (string) ($user->getAttribute('email') ?? '');
        $this->phoneNumber = $this->normalizePhone((string) ($user->getAttribute('contact') ?? ''));
        $this->firstName = (string) ($user->getAttribute('first_name') ?? '');
        $this->lastName = (string) ($user->getAttribute('last_name') ?? '');
        $this->existingProfileImg = $user->profile_img;
        $this->existingGovernmentIdImage = $user->government_id_image;
        $this->governmentIdType = (string) ($user->getAttribute('government_id_type') ?? '');
        $this->governmentIdNumber = (string) ($user->getAttribute('government_id_number') ?? '');
        $this->profilePicture = null;
        $this->governmentIdImage = null;

        $this->syncOriginalState();
        $this->hasPendingChanges = false;
    }

    private function resolveCurrentUser(): ?User
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return null;
        }

        /** @var \App\Models\User|null $freshUser */
        $freshUser = User::query()->find($authUser->getKey());

        return $freshUser ?? $authUser;
    }

    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        // Strip country code if present, then strip leading 9 (shown as prefix in UI).
        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        // Remove leading 9 since it's already displayed as a prefix in the input field
        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            return substr($digits, 1);
        }

        return $digits;
    }

    public function getExistingProfileImgUrlProperty(): ?string
    {
        return $this->resolvePublicFileUrl($this->existingProfileImg);
    }

    public function getExistingGovernmentIdImageUrlProperty(): ?string
    {
        return $this->resolvePublicFileUrl($this->existingGovernmentIdImage);
    }

    private function resolvePublicFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalized = $this->normalizeStoragePath($path);

        return asset('storage/' . $normalized);
    }

    private function normalizeStoragePath(string $path): string
    {
        $normalized = ltrim(trim($path), '/');

        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            $normalized = ltrim((string) parse_url($normalized, PHP_URL_PATH), '/');
        }

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }

        return $normalized;
    }

    public function removeProfilePicture(): void
    {
        $this->profilePicture = null;
        $this->existingProfileImg = null;
        $this->recomputePendingChanges();
    }

    public function clearAllFields(): void
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->phoneNumber = '';
        $this->email = '';
        $this->governmentIdType = '';
        $this->governmentIdNumber = '';
        $this->recomputePendingChanges();
    }

    public function removeGovernmentIdImage(): void
    {
        $this->governmentIdImage = null;
        $this->existingGovernmentIdImage = null;
        $this->recomputePendingChanges();
    }

    private function syncOriginalState(): void
    {
        $this->originalFirstName = trim((string) $this->firstName);
        $this->originalLastName = trim((string) $this->lastName);
        $this->originalEmail = trim((string) $this->email);
        $this->originalPhoneNumber = $this->normalizePhone((string) $this->phoneNumber);
        $this->originalProfileImg = $this->existingProfileImg;
        $this->originalGovernmentIdImage = $this->existingGovernmentIdImage;
        $this->originalGovernmentIdType = $this->governmentIdType;
        $this->originalGovernmentIdNumber = $this->governmentIdNumber;
    }

    private function recomputePendingChanges(): void
    {
        $formFirstName = trim((string) $this->firstName);
        $formLastName = trim((string) $this->lastName);
        $formEmail = trim((string) $this->email);
        $formPhone = $this->normalizePhone((string) $this->phoneNumber);

        $this->hasPendingChanges = $formFirstName !== $this->originalFirstName
            || $formLastName !== $this->originalLastName
            || $formEmail !== $this->originalEmail
            || $formPhone !== $this->originalPhoneNumber
            || $this->profilePicture !== null
            || $this->governmentIdImage !== null
            || $this->existingProfileImg !== $this->originalProfileImg
            || $this->existingGovernmentIdImage !== $this->originalGovernmentIdImage
            || $this->governmentIdType !== $this->originalGovernmentIdType
            || $this->governmentIdNumber !== $this->originalGovernmentIdNumber;
    }

    public function save(): void
    {
        $this->firstName = trim((string) $this->firstName);
        $this->lastName = trim((string) $this->lastName);
        $this->email = trim((string) $this->email);
        $this->phoneNumber = $this->normalizePhone((string) $this->phoneNumber);

        $user = $this->resolveCurrentUser();

        if (!$user) {
            $this->notifyError('Session expired', 'Please sign in again.');
            return;
        }

        // Fill empty fields with existing DB values so partial updates work
        if ($this->firstName === '') {
            $this->firstName = (string) $user->first_name;
        }
        if ($this->lastName === '') {
            $this->lastName = (string) $user->last_name;
        }
        if ($this->phoneNumber === '') {
            $this->phoneNumber = $this->normalizePhone((string) $user->contact);
        }
        if ($this->email === '') {
            $this->email = (string) $user->email;
        }
        if ($this->governmentIdType === '' && $user->government_id_type) {
            $this->governmentIdType = (string) $user->government_id_type;
        }
        if ($this->governmentIdNumber === '' && $user->government_id_number) {
            $this->governmentIdNumber = (string) $user->government_id_number;
        }

        $this->recomputePendingChanges();

        if (!$this->hasPendingChanges) {
            $this->notifyInfo('No changes detected', 'Update any field before saving.');
            return;
        }

        try {
            $this->validate([
                'firstName' => 'nullable|string|max:255',
                'lastName' => 'nullable|string|max:255',
                'phoneNumber' => [
                    'required',
                    'digits:9',
                    function ($attribute, $value, $fail) use ($user) {
                        $fullNumber = '9' . preg_replace('/\D/', '', $value);
                        $exists = \App\Models\User::where('contact', $fullNumber)
                            ->where('user_id', '!=', $user->user_id)
                            ->exists();
                        if ($exists) {
                            $fail('This phone number is already registered.');
                        }
                    },
                ],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
                'profilePicture' => 'nullable|image|max:10240',
                'governmentIdImage' => 'nullable|image|max:10240',
                'governmentIdType' => 'nullable|string|max:255',
                'governmentIdNumber' => 'nullable|string|max:255',
            ]);

            $updateData = [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'contact' => '9' . $this->phoneNumber,
                'government_id_type' => $this->governmentIdType ?: null,
                'government_id_number' => $this->governmentIdNumber ?: null,
            ];

            // Handle profile picture upload
            if ($this->profilePicture) {
                // Note: Only delete old image when uploading a new one
                if ($user->profile_img && $this->existingProfileImg !== $user->profile_img) {
                    $this->deleteStoredImage($user->profile_img);
                }
                $storedPath = $this->profilePicture->store('profile-photos', 'public');
                $updateData['profile_img'] = $storedPath;

                // Sync to public/storage
                $sourcePath = storage_path('app/public/' . $storedPath);
                $destPath = public_path('storage/' . $storedPath);
                $publicDir = dirname($destPath);

                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }

                if (file_exists($sourcePath)) {
                    copy($sourcePath, $destPath);
                }
            } elseif ($this->existingProfileImg === null && $user->profile_img) {
                // Only delete if user explicitly removed the image
                $this->deleteStoredImage($user->profile_img);
                $updateData['profile_img'] = null;
            }

            // Handle government ID image upload
            if ($this->governmentIdImage) {
                // Note: Only delete old image when uploading a new one
                if ($user->government_id_image && $this->existingGovernmentIdImage !== $user->government_id_image) {
                    $this->deleteStoredImage($user->government_id_image);
                }
                $storedPath = $this->governmentIdImage->store('government-ids', 'public');
                $updateData['government_id_image'] = $storedPath;
                Log::info('Government ID stored', ['path' => $storedPath]);

                // Sync to public/storage if it's not a symlink
                $sourcePath = storage_path('app/public/' . $storedPath);
                $destPath = public_path('storage/' . $storedPath);
                $publicDir = dirname($destPath);

                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }

                if (file_exists($sourcePath)) {
                    copy($sourcePath, $destPath);
                    Log::info('Government ID synced to public', ['dest' => $destPath]);
                } else {
                    Log::warning('Government ID source not found', ['source' => $sourcePath]);
                }
            } elseif ($this->existingGovernmentIdImage === null && $user->government_id_image) {
                // Only delete if user explicitly removed the image
                $this->deleteStoredImage($user->government_id_image);
                $updateData['government_id_image'] = null;
            }

            $user->update($updateData);
            $user->refresh();

            $this->profilePicture = null;
            $this->governmentIdImage = null;
            $this->existingProfileImg = $user->profile_img;
            $this->existingGovernmentIdImage = $user->government_id_image;
            $this->syncOriginalState();
            $this->hasPendingChanges = false;

            // Auto-dismiss valid ID notification if all ID fields are now complete
            if ($user->government_id_type && $user->government_id_number && $user->government_id_image) {
                NotificationModel::where('user_id', $user->user_id)
                    ->where('type', 'valid_id_required')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }

            $this->dispatch('profile-updated');
            $this->notifySuccess('Settings Saved Successfully!', 'Your personal information has been updated.');
        } catch (\Throwable $exception) {
            Log::error('Settings save failed.', [
                'user_id' => $user->user_id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function cancelSave(): void
    {
        $this->loadUserData();
        $this->resetValidation();
    }

    private function deleteStoredImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            $normalized = $this->normalizeStoragePath($path);

            if ($normalized !== '' && Storage::disk('public')->exists($normalized)) {
                Storage::disk('public')->delete($normalized);
            }
        } catch (\Throwable $exception) {
            // File may not exist on Render ephemeral filesystem after redeploy
            Log::debug('Could not delete stored image (may be expected on Render redeploy).', [
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.forms.owner-details');
    }
}
