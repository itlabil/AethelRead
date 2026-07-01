@extends('admin.layouts.app')

@section('title', 'Add Novel')
@section('header', 'Add Novel')
@section('subheader', 'Create a new novel')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <form method="POST" action="{{ route('admin.novels.store') }}" enctype="multipart/form-data">
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

            {{-- Cover --}}
            <div class="mb-5" x-data="imageUpload()">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cover Image
                    <span class="text-gray-400 font-normal">(max 2MB, jpeg/jpg/png/webp, recommended 300x420px)</span>
                </label>

                <div
                    class="relative border-2 border-dashed rounded-xl p-6 text-center transition"
                    :class="isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-300 hover:border-primary-300'"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                >
                    <div x-show="preview" class="mb-4">
                        <img :src="preview" class="w-20 h-28 rounded-lg object-cover mx-auto border border-gray-200"/>
                        <p class="text-xs text-gray-500 mt-2" x-text="fileName"></p>
                    </div>
                    <div x-show="!preview">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">
                            Drag & drop or
                            <label for="cover" class="text-primary-600 hover:text-primary-700 cursor-pointer font-medium">browse</label>
                        </p>
                    </div>
                    <input
                        type="file"
                        id="cover"
                        name="cover"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        @change="handleFile($event)"
                    />
                </div>
                <button type="button" x-show="preview" @click="clearPreview()" class="mt-2 text-xs text-gray-400 hover:text-red-500 transition">
                    Clear selection
                </button>
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