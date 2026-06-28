@extends('admin.layouts.app')

@section('title', 'Novels')
@section('header', 'Novels')
@section('subheader', 'Manage all novels in Aethel Read')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

    {{-- Table Header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Total <span class="font-semibold text-gray-800">{{ $novels->total() }}</span> novels
        </p>
        
        <a href="{{ route('admin.novels.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Novel
        </a>
    </div>

    {{-- Filters --}}
    <x-admin.table-filters
        :route="route('admin.novels.index')"
        :filters="$filters"
        :show-type="true"
        :types="['manga' => 'Manga', 'manhwa' => 'Manhwa', 'manhua' => 'Manhua', 'other' => 'Other']"
    />

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($novels as $novel)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $novel->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700">
                                {{ ucfirst($novel->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $novel->slug }}</td>
                        <td class="px-6 py-4">
                            @include('admin.partials.badge', ['active' => $novel->is_active])
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $novel->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Toggle --}}
                                <form method="POST" action="{{ route('admin.novels.toggle', $novel->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border transition
                                            {{ $novel->is_active
                                                ? 'border-gray-200 text-gray-500 hover:bg-gray-50'
                                                : 'border-green-200 text-green-600 hover:bg-green-50' }}"
                                    >
                                        {{ $novel->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                {{-- Edit --}}
                                
                                <a href="{{ route('admin.novels.edit', $novel->id) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-primary-200 text-primary-600 hover:bg-primary-50 transition"
                                >
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.novels.destroy', $novel->id) }}"
                                    onsubmit="event.preventDefault(); confirmDelete(this)"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            No novels found.
                            <a href="{{ route('admin.novels.create') }}" class="text-primary-600 hover:underline ml-1">Add one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($novels->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $novels->links() }}
        </div>
    @endif

</div>
@endsection