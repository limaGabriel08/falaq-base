<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Pergunta;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UpvotePerguntaTest extends TestCase
{
    use RefreshDatabase;

    private Evento $evento;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->evento = Evento::create([
            'titulo' => 'Evento de Teste',
            'descricao' => 'Evento usado nos testes de votos',
        ]);
    }

    private function criarPergunta(string $texto): Pergunta
    {
        return Pergunta::create([
            'evento_id' => $this->evento->id,
            'texto' => $texto,
            'status' => 'aprovado',
            'is_public' => true,
        ]);
    }

    public function test_migration_cria_tabela_pivot_com_chave_unica(): void
    {
        $this->assertTrue(Schema::hasTable('pergunta_user'));
        $this->assertTrue(Schema::hasColumns('pergunta_user', ['pergunta_id', 'user_id', 'created_at', 'updated_at']));

        $user = User::factory()->create();
        $pergunta = $this->criarPergunta('Pergunta para testar a chave única');

        $pergunta->votos()->attach($user->id);

        $this->expectException(QueryException::class);
        $pergunta->votos()->attach($user->id);
    }

    public function test_pergunta_possui_relacionamento_votos(): void
    {
        $pergunta = $this->criarPergunta('Pergunta para testar o relacionamento');

        $this->assertInstanceOf(BelongsToMany::class, $pergunta->votos());
    }

    public function test_user_possui_relacionamento_perguntas_votadas(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $user->perguntasVotadas());
    }

    public function test_visitante_nao_pode_votar(): void
    {
        $pergunta = $this->criarPergunta('Pergunta que o visitante tenta votar');

        $this->post(route('perguntas.votar', $pergunta))
            ->assertRedirect(route('login.create'));

        $this->assertDatabaseCount('pergunta_user', 0);
    }

    public function test_votar_registra_voto_na_pivot(): void
    {
        $user = User::factory()->create();
        $pergunta = $this->criarPergunta('Pergunta que vai receber um voto');

        $this->actingAs($user)
            ->from(route('eventos.show', $this->evento->id))
            ->post(route('perguntas.votar', $pergunta))
            ->assertRedirect(route('eventos.show', $this->evento->id))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('pergunta_user', [
            'pergunta_id' => $pergunta->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_votar_novamente_remove_voto_da_pivot(): void
    {
        $user = User::factory()->create();
        $pergunta = $this->criarPergunta('Pergunta que vai perder o voto');

        $this->actingAs($user)->post(route('perguntas.votar', $pergunta));
        $this->actingAs($user)->post(route('perguntas.votar', $pergunta));

        $this->assertDatabaseMissing('pergunta_user', [
            'pergunta_id' => $pergunta->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_perguntas_sao_ordenadas_por_votos(): void
    {
        $usuarios = User::factory()->count(3)->create();

        $semVotos = $this->criarPergunta('Pergunta com zero votos');
        $tresVotos = $this->criarPergunta('Pergunta com tres votos');
        $umVoto = $this->criarPergunta('Pergunta com um voto');

        $tresVotos->votos()->attach($usuarios->pluck('id'));
        $umVoto->votos()->attach($usuarios->first()->id);

        $this->get(route('eventos.show', $this->evento->id))
            ->assertOk()
            ->assertSeeInOrder([
                'Pergunta com tres votos',
                'Pergunta com um voto',
                'Pergunta com zero votos',
            ]);
    }

    public function test_botao_fica_destacado_quando_usuario_ja_votou(): void
    {
        $user = User::factory()->create();
        $pergunta = $this->criarPergunta('Pergunta votada pelo usuario');

        $this->actingAs($user)
            ->get(route('eventos.show', $this->evento->id))
            ->assertSee('▲ Votar');

        $pergunta->votos()->attach($user->id);

        $this->actingAs($user)
            ->get(route('eventos.show', $this->evento->id))
            ->assertSee('▲ Votado (clique para remover)')
            ->assertSee('1 voto');
    }
}
