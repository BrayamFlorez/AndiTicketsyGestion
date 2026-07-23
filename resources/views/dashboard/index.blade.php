{{-- resources/views/dashboard/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    @include('dashboard.partials.' . $partial)
</x-app-layout>
