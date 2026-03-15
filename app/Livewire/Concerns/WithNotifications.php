<?php

namespace App\Livewire\Concerns;

trait WithNotifications
{
    /**
     * Show a success notification
     *
     * @param string $title The title of the notification
     * @param string|null $description Optional description
     * @param int $duration Duration in milliseconds
     */
    public function notifySuccess(string $title, ?string $description = null, int $duration = 4000): void
    {
        $this->dispatch('notify', type: 'success', title: $title, description: $description, duration: $duration);
    }

    /**
     * Show an error notification
     *
     * @param string $title The title of the notification
     * @param string|null $description Optional description
     * @param int $duration Duration in milliseconds
     */
    public function notifyError(string $title, ?string $description = null, int $duration = 5000): void
    {
        $this->dispatch('notify', type: 'error', title: $title, description: $description, duration: $duration);
    }

    /**
     * Show a warning notification
     *
     * @param string $title The title of the notification
     * @param string|null $description Optional description
     * @param int $duration Duration in milliseconds
     */
    public function notifyWarning(string $title, ?string $description = null, int $duration = 4000): void
    {
        $this->dispatch('notify', type: 'warning', title: $title, description: $description, duration: $duration);
    }

    /**
     * Show an info notification
     *
     * @param string $title The title of the notification
     * @param string|null $description Optional description
     * @param int $duration Duration in milliseconds
     */
    public function notifyInfo(string $title, ?string $description = null, int $duration = 4000): void
    {
        $this->dispatch('notify', type: 'info', title: $title, description: $description, duration: $duration);
    }
}
