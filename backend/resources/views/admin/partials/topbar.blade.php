<header class="bg-white border-b border-gray-200 px-4 lg:px-6 py-4 flex items-center justify-between shrink-0">

    <div class="flex items-center gap-3">

        {{-- Hamburger (Mobile only) --}}
        <button
            @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page Title --}}
        <h2 class="text-sm font-medium text-gray-500 hidden sm:block">
            @yield('title', 'Dashboard')
        </h2>

    </div>

    {{-- Right Side --}}
    <div class="flex items-center gap-4" x-data="{ open: false }">
        <div class="relative">
            <button
                @click="open = !open"
                class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900 transition"
            >
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-700 font-semibold text-xs">
                        {{ strtoupper(substr(auth('web')->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="text-left hidden sm:block">
                    <p class="font-medium text-gray-800 text-sm leading-tight">{{ auth('web')->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst(auth('web')->user()->role) }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-50"
            >
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-xs text-gray-500 truncate">{{ auth('web')->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>