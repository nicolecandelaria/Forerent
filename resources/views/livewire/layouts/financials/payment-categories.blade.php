<div class="w-full font-sans">

    {{-- Header with Add Button --}}
    <x-ui.card-with-tabs
        :tabs="[]"
        :activeTab="'all'"
        title="Payment Categories"
    >
        <x-slot:filters>
            <x-ui.search-bar
                model="search"
                placeholder="Search category name or description..."
            />

            {{-- Type Filter --}}
            <x-dropdown label="{{ $filterType ? ucfirst($filterType) : 'All Types' }}" tooltip="Filter by category type">
                <x-dropdown-item wire:click="$set('filterType', null)" @click="open = false">
                    All Types
                </x-dropdown-item>
                <x-dropdown-item wire:click="$set('filterType', 'income')" @click="open = false" :active="$filterType === 'income'">
                    Income
                </x-dropdown-item>
                <x-dropdown-item wire:click="$set('filterType', 'expense')" @click="open = false" :active="$filterType === 'expense'">
                    Expense
                </x-dropdown-item>
            </x-dropdown>

            {{-- Add Category Button --}}
            <button
                wire:click="openModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#070589] hover:bg-[#000060] text-white text-sm font-semibold rounded-xl shadow-sm transition-colors duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Category
            </button>
        </x-slot:filters>

        {{-- Data Table --}}
        <x-ui.table>
            <x-slot:head>
                <x-ui.th class="w-[5%]">#</x-ui.th>
                <x-ui.th class="w-[22%]">Name</x-ui.th>
                <x-ui.th class="w-[30%]">Description</x-ui.th>
                <x-ui.th class="w-[13%]">Type</x-ui.th>
                <x-ui.th class="w-[12%]">Status</x-ui.th>
                <x-ui.th class="w-[18%]">Actions</x-ui.th>
            </x-slot:head>

            <x-slot:body>
                @forelse ($categories as $index => $category)
                    <x-ui.tr>
                        <x-ui.td>{{ $categories->firstItem() + $index }}</x-ui.td>
                        <x-ui.td isHeader="true">{{ $category->name }}</x-ui.td>
                        <x-ui.td class="truncate max-w-xs" title="{{ $category->description }}">
                            {{ $category->description ?? '—' }}
                        </x-ui.td>
                        <x-ui.td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        </x-ui.td>
                        <x-ui.td>
                            <button wire:click="toggleActive({{ $category->payment_category_id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer transition-colors
                                {{ $category->is_active ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </x-ui.td>
                        <x-ui.td>
                            <div class="flex items-center gap-2">
                                <flux:tooltip content="Edit category" position="bottom">
                                    <button
                                        wire:click="editCategory({{ $category->payment_category_id }})"
                                        class="inline-flex items-center px-3 py-1 border border-[#0906ae] text-[#0906ae] rounded-md text-xs font-bold hover:bg-blue-50 transition-colors"
                                    >
                                        Edit
                                    </button>
                                </flux:tooltip>
                                <flux:tooltip content="Delete category" position="bottom">
                                    <button
                                        wire:click="confirmDelete({{ $category->payment_category_id }})"
                                        class="inline-flex items-center px-3 py-1 border border-red-500 text-red-500 rounded-md text-xs font-bold hover:bg-red-50 transition-colors"
                                    >
                                        Delete
                                    </button>
                                </flux:tooltip>
                            </div>
                        </x-ui.td>
                    </x-ui.tr>
                @empty
                    <x-ui.tr>
                        <x-ui.td colspan="6" class="text-center text-gray-400 py-8">
                            No payment categories found.
                        </x-ui.td>
                    </x-ui.tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        {{-- Pagination --}}
        <x-slot:footer>
            {{ $categories->onEachSide(1)->links('livewire.layouts.components.paginate-blue') }}
        </x-slot:footer>
    </x-ui.card-with-tabs>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6 z-10">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Payment Category' : 'Add Payment Category' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#070589] focus:ring-[#070589] text-sm"
                            placeholder="e.g. Move-In Payment"
                        >
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            wire:model="description"
                            rows="2"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#070589] focus:ring-[#070589] text-sm"
                            placeholder="Brief description of this category..."
                        ></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select
                            wire:model="type"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#070589] focus:ring-[#070589] text-sm"
                        >
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Active Toggle --}}
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#070589]"></div>
                        </label>
                        <span class="text-sm text-gray-700">Active</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#070589] hover:bg-[#000060] rounded-lg transition-colors"
                        >
                            {{ $editingId ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/50" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 z-10 text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Category?</h3>
                <p class="text-sm text-gray-500 mb-5">Are you sure you want to delete this payment category? This action cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <button
                        wire:click="cancelDelete"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="deleteCategory"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
