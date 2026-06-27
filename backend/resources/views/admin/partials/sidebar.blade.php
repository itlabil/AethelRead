<aside x-data="{ open: true }" :class="open ? 'w-64' : 'w-16'"
    class="bg-primary-950 text-white flex flex-col transition-all duration-300 ease-in-out shrink-0">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-primary-800">
        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center shrink-0">
            <span class="text-white font-bold text-sm">A</span>
        </div>
        <div x-show="open" x-transition class="overflow-hidden">
            <p class="font-bold text-white text-sm leading-tight">Aethel Read</p>
            <p class="text-primary-400 text-xs">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-4 overflow-y-auto">

        {{-- Dashboard --}}
        <div class="px-3 mb-1">

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.dashboard')
    ? 'bg-primary-700 text-white'
    : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                {{-- Dashboard Icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="open" x-transition class="overflow-hidden whitespace-nowrap">Dashboard</span>
            </a>
        </div>

        {{-- Section Label --}}
        <div x-show="open" class="px-6 pt-4 pb-1">
            <p class="text-xs font-semibold text-primary-500 uppercase tracking-wider">Content</p>
        </div>

        {{-- Novels --}}
        <div class="px-3 mb-1">
            <a href="{{ route('admin.novels.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.novels.*')
    ? 'bg-primary-700 text-white'
    : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                {{-- Book Icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span x-show="open" x-transition class="overflow-hidden whitespace-nowrap">Novels</span>
            </a>
        </div>

        {{-- Entities --}}
        <div class="px-3 mb-1">

            <a href="{{ route('admin.entities.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.entities.*')
    ? 'bg-primary-700 text-white'
    : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                {{-- Users Icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="open" x-transition class="overflow-hidden whitespace-nowrap">Entities</span>
            </a>
        </div>

        {{-- Section Label --}}
        <div x-show="open" class="px-6 pt-4 pb-1">
            <p class="text-xs font-semibold text-primary-500 uppercase tracking-wider">System</p>
        </div>

        {{-- Users --}}
        <div class="px-3 mb-1">

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.users.*')
    ? 'bg-primary-700 text-white'
    : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                {{-- Shield Icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span x-show="open" x-transition class="overflow-hidden whitespace-nowrap">Users</span>
            </a>
        </div>

    </nav>

    {{-- Toggle Sidebar --}}
    <div class="border-t border-primary-800 p-3">
        <button @click="open = !open"
            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-primary-400 hover:bg-primary-800 hover:text-white transition text-sm">
            <svg x-show="open" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg x-show="!open" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
            <span x-show="open" x-transition class="overflow-hidden whitespace-nowrap text-xs">Collapse</span>
        </button>
    </div>

</aside>