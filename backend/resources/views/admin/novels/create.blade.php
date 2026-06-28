@extends('admin.layouts.app')

@section('title', 'Add Novel')
@section('header', 'Add Novel')
@section('subheader', 'Create a new novel')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <form method="POST" action="{{ route('admin.novels.store') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Novel Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition
                        {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}"
                    placeholder="e.g. Nano Machine"
                    autofocus
                />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div class="mb-5">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type <span class="text-red-500">*</span>
                </label>
                <select
                    id="type"
                    name="type"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition
                        {{ $errors->has('type') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}"
                >
                    <option value="">Select type</option>
                    @foreach (['manga', 'manhwa', 'manhua', 'other'] as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Active --}}
            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', '1') ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                    />
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
                >
                    Save Novel
                </button>
                
                    <a href="{{ route('admin.novels.index') }}"
                    class="px-6 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition"
                >
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
@endsection