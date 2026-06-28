{{-- Desktop Sidebar --}}
<aside
    class="hidden lg:flex flex-col bg-primary-950 text-white transition-all duration-300 ease-in-out shrink-0"
    :class="sidebarCollapsed ? 'w-16' : 'w-64'"
>
    @include('admin.partials.sidebar-content')
</aside>

{{-- Mobile Sidebar (Drawer) --}}
<aside
    class="fixed inset-y-0 left-0 z-30 flex flex-col bg-primary-950 text-white w-72 lg:hidden transform transition-transform duration-300 ease-in-out"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    {{-- Close Button --}}
    <button
        @click="sidebarOpen = false"
        class="absolute top-4 right-4 text-primary-400 hover:text-white transition"
    >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    @include('admin.partials.sidebar-content', ['mobile' => true])
</aside>