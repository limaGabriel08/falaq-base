<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModeracaoRequest;
use App\Models\Evento;
use App\Models\Moderacao;
use App\Models\Pergunta;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ModeracaoController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreModeracaoRequest $request, Pergunta $pergunta)
    {
        $pergunta->moderacoes()->create([
            'motivo' => $request->validated('motivo'),
        ]);

        return back()->with('sucesso', 'Denúncia registrada com sucesso!');
    }

    public function index(Evento $evento)
    {
        $this->authorize('moderate', $evento);

        $moderacoes = Moderacao::with('pergunta')
            ->whereHas('pergunta', fn ($query) => $query->where('evento_id', $evento->id))
            ->latest()
            ->paginate(15);

        return view('moderacoes.index', compact('evento', 'moderacoes'));
    }

    public function updateStatus(Request $request, Moderacao $moderacao)
    {
        $this->authorize('moderate', $moderacao->pergunta->evento);

        $request->validate([
            'status' => ['required', 'in:aprovada,rejeitada'],
        ]);

        $moderacao->update(['status' => $request->input('status')]);

        return back()->with('sucesso', 'Status da moderação atualizado!');
    }
}
