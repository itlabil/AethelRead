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
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cover</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Type</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($novels as $novel)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Cover --}}
                        <td class="px-6 py-3">
                            <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                @if ($novel->cover_url)
                                    <img
                                        src="{{ $novel->cover_url }}"
                                        alt="{{ $novel->name }}"
                                        class="w-full h-full object-cover"
                                    />
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                @endif
                            </div>
                        </td>

                        {{-- Name --}}
                        <td class="px-6 py-3">
                            
                            <a href="{{ route('admin.novels.entities.index', $novel->id) }}"
                                class="font-medium text-gray-900 hover:text-primary-600 transition"
                            >
                                {{ $novel->name }}
                            </a>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $novel->slug }}</p>
                        </td>

                        {{-- Type --}}
                        <td class="px-6 py-3 hidden md:table-cell">
                            @include('admin.partials.type-badge', ['type' => $novel->type])
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-3 hidden lg:table-cell">
                            @include('admin.partials.badge', ['active' => $novel->is_active])
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end gap-1">

                                {{-- Entities --}}
                                
                                <a href="{{ route('admin.novels.entities.index', $novel->id) }}"
                                    class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition"
                                    title="Manage Entities"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>

                                {{-- Toggle --}}
                                <form method="POST" action="{{ route('admin.novels.toggle', $novel->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="p-2 rounded-lg transition {{ $novel->is_active ? 'text-gray-400 hover:bg-gray-50' : 'text-green-500 hover:bg-green-50' }}"
                                        title="{{ $novel->is_active ? 'Deactivate' : 'Activate' }}"
                                    >
                                        @if ($novel->is_active)
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                {{-- Edit --}}
                                
                                <a href="{{ route('admin.novels.edit', $novel->id) }}"
                                    class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.novels.destroy', $novel->id) }}"
                                    id="form-delete-novel-{{ $novel->id }}"
                                    class="hidden"
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button
                                    type="button"
                                    onclick="confirmDelete(document.getElementById('form-delete-novel-{{ $novel->id }}'))"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition"
                                    title="Delete"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
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
            @include('admin.partials.pagination', ['paginator' => $novels])
        </div>
    @endif

</div>
@endsection