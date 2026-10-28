<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Moderacao;
use App\Models\Pergunta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeracaoTest extends TestCase
{
    use RefreshDatabase;

    private function cenario(): array
    {
        $dono = User::factory()->create();
        $participante = User::factory()->create();
        $evento = Evento::create(['titulo' => 'Evento', 'descricao' => 'Descricao', 'data_evento' => now(), 'user_id' => $dono->id]);
        $pergunta = Pergunta::create(['evento_id' => $evento->id, 'user_id' => $participante->id, 'texto' => 'Pergunta de teste aqui', 'status' => 'pendente', 'is_public' => true]);

        return [$dono, $participante, $evento, $pergunta];
    }

    public function test_participante_registra_denuncia_com_status_padrao(): void
    {
        [, $participante, , $pergunta] = $this->cenario();

        $this->actingAs($participante)->post(route('perguntas.moderar', $pergunta), ['motivo' => 'Conteúdo ofensivo demais'])->assertRedirect();

        $this->assertDatabaseHas('moderacoes', ['pergunta_id' => $pergunta->id, 'status' => 'analisando']);
    }

    public function test_motivo_e_validado(): void
    {
        [, $participante, , $pergunta] = $this->cenario();

        $this->actingAs($participante)->post(route('perguntas.moderar', $pergunta), ['motivo' => 'curto'])->assertSessionHasErrors('motivo');
        $this->assertDatabaseCount('moderacoes', 0);
    }

    public function test_visitante_nao_denuncia(): void
    {
        [, , , $pergunta] = $this->cenario();

        $this->post(route('perguntas.moderar', $pergunta), ['motivo' => 'Conteúdo ofensivo demais'])->assertRedirect();
        $this->assertDatabaseCount('moderacoes', 0);
    }

    public function test_apenas_dono_ve_a_fila(): void
    {
        $this->withoutVite();
        [$dono, $participante, $evento, $pergunta] = $this->cenario();
        $pergunta->moderacoes()->create(['motivo' => 'Conteúdo ofensivo demais']);

        $this->actingAs($participante)->get(route('eventos.moderacoes.index', $evento))->assertForbidden();
        $this->actingAs($dono)->get(route('eventos.moderacoes.index', $evento))->assertOk()->assertSee('Conteúdo ofensivo demais');
    }

    public function test_apenas_dono_altera_status(): void
    {
        [$dono, $participante, , $pergunta] = $this->cenario();
        $moderacao = $pergunta->moderacoes()->create(['motivo' => 'Conteúdo ofensivo demais']);

        $this->actingAs($participante)->patch(route('moderacoes.status', $moderacao), ['status' => 'aprovada'])->assertForbidden();
        $this->actingAs($dono)->patch(route('moderacoes.status', $moderacao), ['status' => 'rejeitada'])->assertRedirect();

        $this->assertSame('rejeitada', $moderacao->fresh()->status);
    }

    public function test_relacionamentos(): void
    {
        [, , , $pergunta] = $this->cenario();
        $moderacao = Moderacao::create(['pergunta_id' => $pergunta->id, 'motivo' => 'Conteúdo ofensivo demais']);

        $this->assertTrue($moderacao->pergunta->is($pergunta));
        $this->assertCount(1, $pergunta->moderacoes);
    }
}
