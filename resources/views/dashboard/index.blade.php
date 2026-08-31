<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Partial dinámico cargado según el rol --}}
    @include('dashboard.partials.' . $partial)
</x-app-layout>