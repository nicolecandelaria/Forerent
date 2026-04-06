<?php

namespace App\Livewire\Layouts\Financials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaymentCategory;

class PaymentCategories extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = null;

    // Form fields
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $description = '';
    public $type = 'income';
    public $is_active = true;

    // Delete confirmation
    public $confirmingDelete = false;
    public $deletingId = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:income,expense',
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editCategory($id)
    {
        $category = PaymentCategory::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->type = $category->type;
        $this->is_active = $category->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        PaymentCategory::updateOrCreate(
            ['payment_category_id' => $this->editingId],
            [
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'is_active' => $this->is_active,
            ]
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deletingId = $id;
    }

    public function deleteCategory()
    {
        if ($this->deletingId) {
            PaymentCategory::findOrFail($this->deletingId)->delete();
        }
        $this->confirmingDelete = false;
        $this->deletingId = null;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $category = PaymentCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->type = 'income';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $categories = PaymentCategory::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.layouts.financials.payment-categories', [
            'categories' => $categories,
        ]);
    }
}
