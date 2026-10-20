@extends('layouts.app')

@section('title', 'Criar Evento — FalaQ')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Criar Novo Evento</h2>

    <form action="{{ route('eventos.store') }}" method="POST">
        @csrf

        <div class="mb-5">
            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input
                type="text"
                name="titulo"
                id="titulo"
                value="{{ old('titulo') }}"
                class="w-full border @error('titulo') border-red-500 @else border-gray-300 @enderror rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Nome do evento"
            >
            @error('titulo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea
                name="descricao"
                id="descricao"
                rows="4"
                class="w-full border @error('descricao') border-red-500 @else border-gray-300 @enderror rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Descreva o evento..."
            >{{ old('descricao') }}</textarea>
            @error('descricao')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium">
            Criar Evento
        </button>
    </form>
</div>
@endsection
