<x-app-layout>
    <x-slot name="title">Editar {{ $modelo->nombre_completo }}</x-slot>
    @include('impresoras.catalogo.partials.form')
</x-app-layout>