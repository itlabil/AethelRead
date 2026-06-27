@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-lg font-bold text-primary-700">Aethel Read</span>
            <span class="text-xs text-gray-400 font-medium">Admin Panel</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="text-sm text-red-500 hover:text-red-700 font-medium transition"
                >
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Welcome, {{ auth()->user()->name }}!
            </h1>
            <p class="text-gray-500 text-sm">
                Aethel Read Admin Panel is ready.
            </p>
            <div class="mt-4 inline-flex items-center gap-2 bg-primary-50 text-primary-700 text-xs font-medium px-4 py-2 rounded-full">
                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                {{ ucfirst(auth()->user()->role) }}
            </div>
        </div>
    </div>

</div>
@endsection