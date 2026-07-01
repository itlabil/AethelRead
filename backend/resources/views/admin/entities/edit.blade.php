@extends('admin.layouts.app')

@section('title', 'Edit Entity')
@section('header', 'Edit Entity')
@section('subheader', 'Update entity information')

@section('content')

{{-- Back --}}
<div class="mb-4">
    
    <a href="{{ route('admin.novels.entities.index', $novelModel->id) }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition"
    >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to {{ $novelModel->name }}
    </a>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <form method="POST" action="{{ route('admin.novels.entities.update', [$novelModel->id, $entity->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Novel (hidden) --}}
            <input type="hidden" name="novel_id" value="{{ $novelModel->id }}">

            {{-- Novel Info (readonly display) --}}
            <div class="mb-5 p-4 bg-gray-50 rounded-xl flex items-center gap-3">
                <div class="w-8 h-11 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center shrink-0">
                    @if ($novelModel->cover_url)
                        <img src="{{ $novelModel->cover_url }}" alt="{{ $novelModel->name }}" class="w-full h-full object-cover"/>
                    @else
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $novelModel->name }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($novelModel->type) }}</p>
                </div>
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
            <div class="mb-5" x-data="imageUpload()">
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>

                {{-- Current Image --}}
                @if ($entity->image)
                    <div class="mb-4 flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <img
                            src="{{ Storage::url($entity->image->thumbnail_path) }}"
                            alt="{{ $entity->name }}"
                            class="w-16 h-16 rounded-lg object-cover border border-gray-200"
                        />
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Current image</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ number_format($entity->image->size / 1024, 1) }} KB
                                · {{ $entity->image->width }}×{{ $entity->image->height }}px
                            </p>
                        </div>
                        <form
                            method="POST"
                            action="{{ route('admin.novels.entities.image.destroy', [$novelModel->id, $entity->id]) }}"
                            id="form-delete-image"
                            class="hidden"
                        >
                            @csrf
                            @method('DELETE')
                        </form>
                        <button
                            type="button"
                            onclick="confirmDelete(document.getElementById('form-delete-image'))"
                            class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition"
                        >
                            Remove
                        </button>
                    </div>
                @endif

                {{-- Upload New --}}
                <div
                    class="relative border-2 border-dashed rounded-xl p-6 text-center transition"
                    :class="isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-300 hover:border-primary-300'"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                >
                    <div x-show="preview" class="mb-4">
                        <img :src="preview" class="w-24 h-24 rounded-lg object-cover mx-auto border border-gray-200"/>
                        <p class="text-xs text-gray-500 mt-2" x-text="fileName"></p>
                    </div>
                    <div x-show="!preview">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">
                            Drag & drop or
                            <label for="image" class="text-primary-600 hover:text-primary-700 cursor-pointer font-medium">browse</label>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WEBP · max 2MB</p>
                    </div>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        @change="handleFile($event)"
                    />
                </div>
                <button type="button" x-show="preview" @click="clearPreview()" class="mt-2 text-xs text-gray-400 hover:text-red-500 transition">
                    Clear selection
                </button>
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
                
                <a href="{{ route('admin.novels.entities.index', $novelModel->id) }}"
                    class="px-6 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition"
                >
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
@endsection