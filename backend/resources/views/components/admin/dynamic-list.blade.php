@props([
    'name',
    'label',
    'placeholder' => 'Add item...',
    'items'       => [],
])

<div x-data="{
    items: {{ json_encode(array_values($items)) }},
    newItem: '',
    addItem() {
        const val = this.newItem.trim();
        if (val === '') return;
        if (this.items.includes(val)) return;
        this.items.push(val);
        this.newItem = '';
    },
    removeItem(index) {
        this.items.splice(index, 1);
    }
}">

    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>

    {{-- Input tambah item --}}
    <div class="flex gap-2 mb-3">
        <input
            type="text"
            x-model="newItem"
            @keydown.enter.prevent="addItem()"
            placeholder="{{ $placeholder }}"
            class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
        />
        <button
            type="button"
            @click="addItem()"
            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
        >
            Add
        </button>
    </div>

    {{-- List items --}}
    <div class="space-y-2 mb-2">
        <template x-for="(item, index) in items" :key="index">
            <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg border border-gray-200">
                <span class="flex-1 text-sm text-gray-700" x-text="item"></span>
                <button
                    type="button"
                    @click="removeItem(index)"
                    class="text-gray-400 hover:text-red-500 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Hidden input untuk dikirim ke server --}}
                <input type="hidden" :name="`{{ $name }}[]`" :value="item" />
            </div>
        </template>
    </div>

    {{-- Empty state --}}
    <p x-show="items.length === 0" class="text-xs text-gray-400 italic">
        No {{ strtolower($label) }} added yet.
    </p>

</div>