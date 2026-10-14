{{-- resources/views/impresoras/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar impresora {{ $impresora->codigo }}</x-slot>

    <div x-data="{ estadoGuardado: 'inactivo' }">
        <x-oso-saving-loader />
        @include('impresoras.partials.form')
    </div>
</x-app-layout>