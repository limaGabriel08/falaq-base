@extends('layouts.app')

@section('title', 'Fila de Moderação — FalaQ')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold">🛡️ Fila de Moderação</h2>
        <p class="text-muted">{{ $evento->titulo }}</p>
        <a href="{{ route('eventos.show', $evento->id) }}">← Voltar ao evento</a>
    </div>

    @if(session('sucesso'))
        <div class="alert alert-success">{{ session('sucesso') }}</div>
    @endif

    @forelse($moderacoes as $moderacao)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <p class="fs-5 mb-1 text-white">{{ $moderacao->pergunta->texto }}</p>
                <p class="text-secondary small mb-2">Motivo: {{ $moderacao->motivo }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-secondary">{{ $moderacao->status }}</span>
                    <div class="d-flex gap-2">
                        <form action="{{ route('moderacoes.status', $moderacao) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="aprovada">
                            <button type="submit" class="btn btn-sm btn-success">Aprovar</button>
                        </form>
                        <form action="{{ route('moderacoes.status', $moderacao) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejeitada">
                            <button type="submit" class="btn btn-sm btn-danger">Rejeitar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-dark text-center p-4">Nenhuma denúncia na fila.</div>
    @endforelse

    <div class="d-flex justify-content-center mt-4">
        {{ $moderacoes->links() }}
    </div>
@endsection
