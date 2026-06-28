@extends('admin.layouts.app')

@section('title', 'Edit Entity')
@section('header', 'Edit Entity')
@section('subheader', 'Update entity information')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <form method="POST" action="{{ route('admin.entities.update', $entity->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Novel --}}
            <div class="mb-5">
                <label for="novel_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Novel <span class="text-red-500">*</span>
                </label>
                <select
                    id="novel_id"
                    name="novel_id"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-primary-500
                        {{ $errors->has('novel_id') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}"
                >
                    @foreach ($novels as $novel)
                        <option value="{{ $novel->id }}" {{ old('novel_id', $entity->novel_id) === $novel->id ? 'selected' : '' }}>
                            {{ $novel->name }}
                        </option>
                    @endforeach
                </select>
                @error('novel_id')
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
                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-primary-500
                        {{ $errors->has('type') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}"
                >
                    @foreach (['character' => 'Character', 'place' => 'Place', 'item' => 'Item'] as $value => $label)
                        <option value="{{ $value }}" {{ old('type', $entity->type) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $entity->name) }}"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-primary-500
                        {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}"
                />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug (readonly) --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input
                    type="text"
                    value="{{ $entity->slug }}"
                    disabled
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-sm font-mono"
                />
            </div>

            {{-- Aliases --}}
            <div class="mb-5">
                <x-admin.dynamic-list
                    name="aliases"
                    label="Aliases"
                    placeholder="e.g. Heavenly Demon"
                    :items="old('aliases', $entity->aliases->pluck('name')->toArray())"
                />
            </div>

            {{-- Keywords --}}
            <div class="mb-5">
                <x-admin.dynamic-list
                    name="keywords"
                    label="Keywords"
                    placeholder="e.g. cheon"
                    :items="old('keywords', $entity->keywords->pluck('keyword')->toArray())"
                />
            </div>

            {{-- Description EN --}}
            <div class="mb-5">
                <label for="description_en" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                    <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full ml-1">English</span>
                </label>
                <textarea
                    id="description_en"
                    name="description_en"
                    rows="4"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                >{{ old('description_en', $entity->descriptions->firstWhere('locale', 'en')?->content) }}</textarea>
            </div>

            {{-- Description ID --}}
            <div class="mb-5">
                <label for="description_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                    <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full ml-1">Indonesian</span>
                </label>
                <textarea
                    id="description_id"
                    name="description_id"
                    rows="4"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                >{{ old('description_id', $entity->descriptions->firstWhere('locale', 'id')?->content) }}</textarea>
            </div>

            {{-- Image --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>

                {{-- Current Image --}}
                @if ($entity->image)
                    <div class="mb-3 flex items-center gap-4">
                        <img
                            src="{{ $entity->image->thumbnail_url }}"
                            alt="{{ $entity->name }}"
                            class="w-16 h-16 rounded-lg object-cover border border-gray-200"
                        />
                        <div>
                            <p class="text-xs text-gray-500">Current image</p>
                            <p class="text-xs text-gray-400">Upload new to replace</p>
                        </div>
                    </div>
                @endif

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
                @error('image')
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
                        {{ old('is_active', $entity->is_active) ? 'checked' : '' }}
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
                    Update Entity
                </button>
                
                <a href="{{ route('admin.entities.index') }}"
                    class="px-6 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition"
                >
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
@endsection