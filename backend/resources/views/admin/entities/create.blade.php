@extends('admin.layouts.app')

@section('title', 'Add Entity')
@section('header', 'Add Entity')
@section('subheader', 'Create a new entity')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <form method="POST" action="{{ route('admin.entities.store') }}" enctype="multipart/form-data">
            @csrf

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
                    <option value="">Select novel</option>
                    @foreach ($novels as $novel)
                        <option value="{{ $novel->id }}" {{ old('novel_id') === $novel->id ? 'selected' : '' }}>
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
                    <option value="">Select type</option>
                    @foreach (['character' => 'Character', 'place' => 'Place', 'item' => 'Item'] as $value => $label)
                        <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
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
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-primary-500
                        {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}"
                    placeholder="e.g. Cheon Yeo-Woon"
                />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Aliases --}}
            <div class="mb-5">
                <x-admin.dynamic-list
                    name="aliases"
                    label="Aliases"
                    placeholder="e.g. Heavenly Demon"
                    :items="old('aliases', [])"
                />
            </div>

            {{-- Keywords --}}
            <div class="mb-5">
                <x-admin.dynamic-list
                    name="keywords"
                    label="Keywords"
                    placeholder="e.g. cheon"
                    :items="old('keywords', [])"
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
                    placeholder="Enter English description..."
                >{{ old('description_en') }}</textarea>
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
                    placeholder="Masukkan deskripsi Bahasa Indonesia..."
                >{{ old('description_id') }}</textarea>
            </div>

            {{-- Image --}}
            <div class="mb-5" x-data="imageUpload()">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Image
                    <span class="text-gray-400 font-normal">(max 2MB, jpeg/jpg/png/webp)</span>
                </label>

                <div
                    class="relative border-2 border-dashed rounded-xl p-6 text-center transition"
                    :class="isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-300 hover:border-primary-300'"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                >
                    {{-- Preview --}}
                    <div x-show="preview" class="mb-4">
                        <img
                            :src="preview"
                            class="w-24 h-24 rounded-lg object-cover mx-auto border border-gray-200"
                        />
                        <p class="text-xs text-gray-500 mt-2" x-text="fileName"></p>
                    </div>

                    {{-- Placeholder --}}
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

                <button
                    type="button"
                    x-show="preview"
                    @click="clearPreview()"
                    class="mt-2 text-xs text-gray-400 hover:text-red-500 transition"
                >
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
                    Save Entity
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