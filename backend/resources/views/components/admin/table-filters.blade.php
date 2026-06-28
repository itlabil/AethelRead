@props([
    'route',
    'filters',
    'types'    => [],
    'showType' => false,
])

<div class="px-6 py-4 border-b border-gray-100">
    <form method="GET" action="{{ $route }}" x-data>

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">

            {{-- Search --}}
            <div class="flex-1 min-w-48">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Search..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                </div>
            </div>

            {{-- Type Filter --}}
            @if ($showType && count($types) > 0)
                <select
                    name="type"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Types</option>
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" {{ $filters['type'] === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            @endif

            {{-- Status Filter --}}
            <select
                name="status"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                onchange="this.form.submit()"
            >
                <option value="">All Status</option>
                <option value="active"   {{ $filters['status'] === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            {{-- Sort --}}
            <select
                name="sort"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                onchange="this.form.submit()"
            >
                <option value="name"       {{ $filters['sort'] === 'name'       ? 'selected' : '' }}>Sort by Name</option>
                <option value="created_at" {{ $filters['sort'] === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
            </select>

            {{-- Direction --}}
            <select
                name="direction"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                onchange="this.form.submit()"
            >
                <option value="asc"  {{ $filters['direction'] === 'asc'  ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ $filters['direction'] === 'desc' ? 'selected' : '' }}>Descending</option>
            </select>

            {{-- Per Page --}}
            <select
                name="per_page"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                onchange="this.form.submit()"
            >
                <option value="15" {{ $filters['per_page'] === 15 ? 'selected' : '' }}>15 / page</option>
                <option value="30" {{ $filters['per_page'] === 30 ? 'selected' : '' }}>30 / page</option>
                <option value="50" {{ $filters['per_page'] === 50 ? 'selected' : '' }}>50 / page</option>
            </select>

            {{-- Search Button --}}
            <button
                type="submit"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
            >
                Search
            </button>

            {{-- Reset --}}
            @if ($filters['search'] || $filters['status'] || $filters['type'])
                
                <a href="{{ $route }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition"
                >
                    Reset
                </a>
            @endif

        </div>

    </form>
</div>