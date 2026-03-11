<?php

namespace App\Livewire\Layouts;

use Livewire\Component;

/**
 * Generic reusable list panel component
 * Can be used across different pages (maintenance, tenants, etc.) with different data
 *
 * Usage:
 * <livewire:layouts.generic-list-panel
 *     :items="$requests"
 *     item-key="request_id"
 *     active-id-property="activeRequestId"
 *     on-select-method="selectRequest"
 *     title="Maintenance Requests"
 * />
 */
class GenericListPanel extends Component
{
    public $items = [];
    public $itemKey; // Primary key field name (e.g., 'request_id')
    public $activeIdProperty; // Property name to track active item (e.g., 'activeRequestId')
    public $onSelectMethod; // Method name to call on item select (e.g., 'selectRequest')
    public $title; // Panel title
    public $emptyTitle = 'No items found';
    public $emptyDescription = 'There are currently no items in this category.';
    public $parentComponent; // Reference to parent component for dispatching methods

    public function mount($parentComponent, $items, $itemKey, $activeIdProperty, $onSelectMethod, $title, $emptyTitle = null, $emptyDescription = null)
    {
        $this->parentComponent = $parentComponent;
        $this->items = $items;
        $this->itemKey = $itemKey;
        $this->activeIdProperty = $activeIdProperty;
        $this->onSelectMethod = $onSelectMethod;
        $this->title = $title;

        if ($emptyTitle) {
            $this->emptyTitle = $emptyTitle;
        }
        if ($emptyDescription) {
            $this->emptyDescription = $emptyDescription;
        }
    }

    public function selectItem($itemId)
    {
        // Call the parent component's select method
        if ($this->parentComponent && method_exists($this->parentComponent, $this->onSelectMethod)) {
            call_user_func([$this->parentComponent, $this->onSelectMethod], $itemId);
        }
    }

    public function render()
    {
        return view('livewire.layouts.generic-list-panel');
    }
}
