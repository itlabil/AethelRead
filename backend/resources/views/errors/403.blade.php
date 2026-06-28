@extends('admin.layouts.auth')

@section('title', '403 Forbidden')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-4xl font-bold text-yellow-600">403</span>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Forbidden</h1>
        <p class="text-primary-300 text-sm mb-6">
            You do not have permission to access this page.
        </p>
        
        <a href="{{ url('/admin/dashboard') }}"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
        >
            Back to Dashboard
        </a>
    </div>
</div>
@endsection