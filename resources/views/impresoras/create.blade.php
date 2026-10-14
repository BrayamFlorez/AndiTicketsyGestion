{{-- resources/views/impresoras/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nueva impresora</x-slot>

    <div x-data="{ estadoGuardado: 'inactivo' }">
        <x-oso-saving-loader />
        @include('impresoras.partials.form')
    </div>
</x-app-layout>