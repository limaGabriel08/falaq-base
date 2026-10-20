@extends('layouts.app')

@section('title', 'Editar Evento — FalaQ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <!-- Formulário de Edição de Evento -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-4 bg-dark text-white border-secondary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0">✏️ Editar Evento</h4>
                    <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary btn-sm">
                        Voltar
                    </a>
                </div>
                
                <form action="{{ route('eventos.update', $evento) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Título do Evento --}}
                    <div class="mb-3">
                        <label for="titulo" class="form-label text-secondary">Título do Evento</label>
                        <input type="text" 
                               name="titulo" 
                               id="titulo" 
                               class="form-control bg-dark text-white border-secondary @error('titulo') is-invalid @enderror"
                               value="{{ old('titulo', $evento->titulo) }}" 
                               required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Descrição do Evento --}}
                    <div class="mb-3">
                        <label for="descricao" class="form-label text-secondary">Descrição do Evento</label>
                        <textarea name="descricao" 
                                  id="descricao" 
                                  rows="4" 
                                  class="form-control bg-dark text-white border-secondary @error('descricao') is-invalid @enderror">{{ old('descricao', $evento->descricao) }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Data e Hora do Evento --}}
                    <div class="mb-3">
                        <label for="data_evento" class="form-label text-secondary">Data e Hora do Evento</label>
                        <input type="datetime-local" 
                               name="data_evento" 
                               id="data_evento" 
                               class="form-control bg-dark text-white border-secondary @error('data_evento') is-invalid @enderror"
                               value="{{ old('data_evento', \Carbon\Carbon::parse($evento->data_evento)->format('Y-m-d\TH:i')) }}" 
                               required>
                        @error('data_evento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status do Evento --}}
                    <div class="mb-4">
                        <label for="status" class="form-label text-secondary">Status do Evento</label>
                        <select name="status" 
                                id="status" 
                                class="form-select bg-dark text-white border-secondary @error('status') is-invalid @enderror" 
                                required>
                            <option value="rascunho" {{ old('status', $evento->status) === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                            <option value="publicado" {{ old('status', $evento->status) === 'publicado' ? 'selected' : '' }}>Publicado</option>
                            <option value="encerrado" {{ old('status', $evento->status) === 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary w-50 fw-bold">Cancelar</a>
                        <button type="submit" class="btn btn-primary w-50 fw-bold">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection