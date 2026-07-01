@extends('admin.layouts.app')

@section('title', $entity->name)
@section('header', $entity->name)
@section('subheader', 'Entity Detail')

@section('content')
<div class="max-w-3xl space-y-4">

    {{-- Back Button --}}
    <div>
        
        <a href="{{ route('admin.novels.entities.index', $novelModel->id) }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to {{ $novelModel->name }}
        </a>
    </div>

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start gap-6">

            {{-- Image --}}
            <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center shrink-0">
                @if ($entity->image?->thumbnail_path)
                    <img
                        src="{{ Storage::url($entity->image->thumbnail_path) }}"
                        alt="{{ $entity->name }}"
                        class="w-full h-full object-cover"
                    />
                @else
                    <span class="text-3xl font-bold text-gray-300">
                        {{ strtoupper(substr($entity->name, 0, 1)) }}
                    </span>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900">{{ $entity->name }}</h1>
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
                    @include('admin.partials.badge', ['active' => $entity->is_active])
                </div>
                <p class="text-xs text-gray-400 font-mono mt-1">{{ $entity->slug }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    Novel:
                    <a href="{{ route('admin.novels.entities.index', $novelModel->id) }}" class="text-primary-600 hover:underline">
                        {{ $novelModel->name }}
                    </a>
                </p>
            </div>

            {{-- Edit Button --}}
            
            <a href="{{ route('admin.novels.entities.edit', [$novelModel->id, $entity->id]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

        </div>
    </div>

    {{-- Aliases --}}
    @if ($entity->aliases->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Aliases</h2>
            <div class="flex flex-wrap gap-2">
                @foreach ($entity->aliases as $alias)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary-50 text-primary-700">
                        {{ $alias->name }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Keywords --}}
    @if ($entity->keywords->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Keywords</h2>
            <div class="flex flex-wrap gap-2">
                @foreach ($entity->keywords as $keyword)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600 font-mono">
                        {{ $keyword->keyword }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Descriptions --}}
    @if ($entity->descriptions->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Descriptions</h2>
            <div class="space-y-4">
                @foreach ($entity->descriptions as $description)
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $description->locale === 'en' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }} mb-2">
                            {{ $description->locale === 'en' ? 'English' : 'Indonesian' }}
                        </span>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $description->content }}</p>
                    </div>
                    @if (!$loop->last)
                        <hr class="border-gray-100">
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Image Info --}}
    @if ($entity->image)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Image</h2>
            <div class="flex items-center gap-4">
                <img
                    src="{{ Storage::url($entity->image->thumbnail_path) }}"
                    alt="{{ $entity->name }}"
                    class="w-16 h-16 rounded-lg object-cover border border-gray-200"
                />
                <div class="text-sm text-gray-500 space-y-1">
                    <p>Size: {{ number_format($entity->image->size / 1024, 1) }} KB</p>
                    <p>Dimensions: {{ $entity->image->width }}×{{ $entity->image->height }}px</p>
                    <p class="font-mono text-xs text-gray-400">{{ substr($entity->image->hash, 0, 20) }}...</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Meta --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Meta</h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-gray-400 text-xs">Created At</p>
                <p class="text-gray-700">{{ $entity->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Updated At</p>
                <p class="text-gray-700">{{ $entity->updated_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-gray-400 text-xs">Hash</p>
                <p class="text-gray-700 font-mono text-xs break-all">{{ $entity->hash ?? '-' }}</p>
            </div>
        </div>
    </div>

</div>
@endsection