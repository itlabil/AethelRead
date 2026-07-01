@extends('admin.layouts.app')

@section('title', $novel->name . ' — Entities')
@section('header', $novel->name)
@section('subheader', 'Manage entities for this novel')

@section('content')
<div class="space-y-4">

    {{-- Novel Info Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="w-12 h-16 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center shrink-0">
            @if ($novel->cover_url)
                <img src="{{ $novel->cover_url }}" alt="{{ $novel->name }}" class="w-full h-full object-cover"/>
            @else
                <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            @endif
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h2 class="font-semibold text-gray-900">{{ $novel->name }}</h2>
                @include('admin.partials.type-badge', ['type' => $novel->type])
                @include('admin.partials.badge', ['active' => $novel->is_active])
            </div>
            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $novel->slug }}</p>
        </div>
        <div class="flex items-center gap-2">
            
            <a href="{{ route('admin.novels.edit', $novel->id) }}"
                class="px-3 py-1.5 text-xs border border-primary-200 text-primary-600 hover:bg-primary-50 rounded-lg transition"
            >
                Edit Novel
            </a>
            
            <a href="{{ route('admin.novels.index') }}"
                class="px-3 py-1.5 text-xs border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-lg transition"
            >
                ← Back to Novels
            </a>
        </div>
    </div>

    {{-- Entities Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Total <span class="font-semibold text-gray-800">{{ $entities->total() }}</span> entities
            </p>
            
            <a href="{{ route('admin.novels.entities.create', $novel->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Entity
            </a>
        </div>

        {{-- Filters --}}
        <x-admin.table-filters
            :route="route('admin.novels.entities.index', $novel->id)"
            :filters="$filters"
            :show-type="true"
            :types="['character' => 'Character', 'place' => 'Place', 'item' => 'Item']"
        />

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Type</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($entities as $entity)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Image --}}
                            <td class="px-6 py-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                    @if ($entity->image?->thumbnail_path)
                                        <img
                                            src="{{ Storage::url($entity->image->thumbnail_path) }}"
                                            alt="{{ $entity->name }}"
                                            class="w-full h-full object-cover"
                                        />
                                    @else
                                        <span class="text-sm font-bold text-gray-400">
                                            {{ strtoupper(substr($entity->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Name --}}
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900">{{ $entity->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $entity->slug }}</div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-3 hidden md:table-cell">
                                @php
                                    $typeColors = [
                                        'character' => 'bg-blue-100 text-blue-700',
                                        'place'     => 'bg-green-100 text-green-700',
                                        'item'      => 'bg-yellow-100 text-yellow-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$entity->type] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($entity->type) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-3 hidden lg:table-cell">
                                @include('admin.partials.badge', ['active' => $entity->is_active])
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-end gap-1">

                                    {{-- Toggle --}}
                                    <form method="POST" action="{{ route('admin.novels.entities.toggle', [$novel->id, $entity->id]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="p-2 rounded-lg transition {{ $entity->is_active ? 'text-gray-400 hover:bg-gray-50' : 'text-green-500 hover:bg-green-50' }}"
                                            title="{{ $entity->is_active ? 'Deactivate' : 'Activate' }}"
                                        >
                                            @if ($entity->is_active)
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

                                    {{-- Detail --}}
                                    
                                    <a href="{{ route('admin.novels.entities.show', [$novel->id, $entity->id]) }}"
                                        class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-lg transition"
                                        title="View Detail"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    
                                    <a href="{{ route('admin.novels.entities.edit', [$novel->id, $entity->id]) }}"
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
                                        action="{{ route('admin.novels.entities.destroy', [$novel->id, $entity->id]) }}"
                                        id="form-delete-entity-{{ $entity->id }}"
                                        class="hidden"
                                    >
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button
                                        type="button"
                                        onclick="confirmDelete(document.getElementById('form-delete-entity-{{ $entity->id }}'))"
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
                                No entities found for this novel.
                                <a href="{{ route('admin.novels.entities.create', $novel->id) }}" class="text-primary-600 hover:underline ml-1">Add one</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($entities->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                @include('admin.partials.pagination', ['paginator' => $entities])
            </div>
        @endif

    </div>
</div>
@endsection