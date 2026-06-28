@extends('admin.layouts.auth')

@section('title', '404 Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-4xl font-bold text-primary-600">404</span>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Page Not Found</h1>
        <p class="text-primary-300 text-sm mb-6">
            The page you are looking for does not exist.
        </p>
        
        <a href="{{ url('/admin/dashboard') }}"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
        >
            Back to Dashboard
        </a>
    </div>
</div>
@endsection