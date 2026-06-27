<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aethel Read') — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans antialiased" x-data>

    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Topbar --}}
            @include('admin.partials.topbar')

            {{-- Page Content --}}
            <main class="flex-1 p-6">

                {{-- Page Header --}}
                @hasSection('header')
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            @yield('header')
                        </h1>
                        @hasSection('subheader')
                            <p class="text-sm text-gray-500 mt-1">
                                @yield('subheader')
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-init="setTimeout(() => show = false, 4000)"
                        class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between"
                    >
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                        <button @click="show = false" class="text-green-400 hover:text-green-600">✕</button>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-init="setTimeout(() => show = false, 4000)"
                        class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between"
                    >
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                        <button @click="show = false" class="text-red-400 hover:text-red-600">✕</button>
                    </div>
                @endif

                {{-- Content --}}
                @yield('content')

            </main>

        </div>
    </div>

</body>
</html>