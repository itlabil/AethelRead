@extends('admin.layouts.app')

@section('title', 'Entities')
@section('header', 'Entities')
@section('subheader', 'Manage all entities in Aethel Read')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

    {{-- Table Header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Total <span class="font-semibold text-gray-800">{{ $entities->total() }}</span> entities
        </p>
        
        <a href="{{ route('admin.entities.create') }}"
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
        :route="route('admin.entities.index')"
        :filters="$filters"
        :show-type="true"
        :types="['character' => 'Character', 'place' => 'Place', 'item' => 'Item']"
        :novels="$novels"
        :show-novel="true"
    />

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Novel</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Type</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Slug</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($entities as $entity)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $entity->name }}</div>
                            <div class="text-xs text-gray-400 md:hidden">{{ $entity->novel?->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            {{ $entity->novel?->name }}
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $entity->type === 'character' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $entity->type === 'place' ? 'bg-green-50 text-green-700' : '' }}
                                {{ $entity->type === 'item' ? 'bg-yellow-50 text-yellow-700' : '' }}
                            ">
                                {{ ucfirst($entity->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs hidden lg:table-cell">
                            {{ $entity->slug }}
                        </td>
                        <td class="px-6 py-4">
                            @include('admin.partials.badge', ['active' => $entity->is_active])
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Toggle --}}
                                <form method="POST" action="{{ route('admin.entities.toggle', $entity->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border transition
                                            {{ $entity->is_active
                                                ? 'border-gray-200 text-gray-500 hover:bg-gray-50'
                                                : 'border-green-200 text-green-600 hover:bg-green-50' }}"
                                    >
                                        {{ $entity->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                {{-- Edit --}}
                                
                                <a href="{{ route('admin.entities.edit', $entity->id) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-primary-200 text-primary-600 hover:bg-primary-50 transition"
                                >
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.entities.destroy', $entity->id) }}"
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
                            No entities found.
                            <a href="{{ route('admin.entities.create') }}" class="text-primary-600 hover:underline ml-1">Add one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($entities->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $entities->links() }}
        </div>
    @endif

</div>
@endsection