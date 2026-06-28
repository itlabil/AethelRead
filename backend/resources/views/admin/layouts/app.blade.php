<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aethel Read') — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    <div class="min-h-screen flex">

        {{-- Mobile Overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-20 lg:hidden"
        ></div>

        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

            {{-- Topbar --}}
            @include('admin.partials.topbar')

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-6">

                @hasSection('header')
                    <div class="mb-6">
                        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">@yield('header')</h1>
                        @hasSection('subheader')
                            <p class="text-sm text-gray-500 mt-1">@yield('subheader')</p>
                        @endif
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>

    {{-- Toast Notifications --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: { popup: '!rounded-2xl !text-sm' },
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: { popup: '!rounded-2xl !text-sm' },
                });
            });
        </script>
    @endif

</body>
</html>