{{-- resources/views/proyectos/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar {{ $proyecto->codigo }}</x-slot>
    @include('proyectos.partials.form')
</x-app-layout>
