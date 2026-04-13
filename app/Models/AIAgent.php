<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIAgent extends Model
{
    protected $fillable = [
        'name',
        'type',
        'role',
        'endpoint',
        'model',
        'prompt',
        'is_active',
        'timeout_seconds',
        'max_tokens',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'timeout_seconds' => 'integer',
        'max_tokens' => 'integer',
    ];

    public function flowSteps(): HasMany
    {
        return $this->hasMany(AIFlowStep::class, 'agent_id');
    }

    public function executionLogs(): HasMany
    {
        return $this->hasMany(AIExecutionLog::class, 'agent_id');
    }

    public static function getAvailableTypes(): array
    {
        return [
            'local' => 'Local ( Ollama )',
            'kilo' => 'Kilo ( Custom )',
            'openai' => 'OpenAI',
            'anthropic' => 'Anthropic (Claude)',
            'ollama' => 'Ollama',
        ];
    }

    public static function getAvailableRoles(): array
    {
        return [
            'investigator' => 'Investigador',
            'analyzer' => 'Analisador',
            'decision' => 'Decisor',
            'bug_creator' => 'Criador de Bugs',
            'advisor' => 'Consultor',
        ];
    }

    public function getDefaultPrompt(): string
    {
        return match ($this->role) {
            'investigator' => "Você é um investigador de logs e erros.

Analise os dados fornecidos e retorne um JSON com:
- error_type: tipo de erro identificado
- frequency: frequência de ocorrência
- possible_cause: possível causa (sem afirmar)
- impact: impacto estimado
- confidence_level: nível de confiança (0-100)

Não tome decisão final. Apenas colete e analise dados.",
            'analyzer' => "Você é um analista de sistemas.

Cruze os dados fornecidos e melhore o contexto:
- Correlacione informações
- Identifique padrões
- Adicione contexto relevante

Retorne JSON com dados enriquecidos.",
            'decision' => "Você é um engineer DevOps.

Baseado nos dados abaixo, decida:
1. Isso é um bug real?
2. Qual a causa provável?
3. Qual o impacto?
4. Deve criar bug automaticamente?

Responda JSON:
{
  \"is_bug\": true/false,
  \"title\": \"\",
  \"description\": \"\",
  \"severity\": \"low|medium|high|critical\",
  \"recommendation\": \"\"
}",
            'bug_creator' => "Você é um especialista em criação de bugs.

Crie um bug detalhado com:
- título claro
- descrição completa
- passos para reproduzir
- logs relevantes
- sugestão de solução

Retorne JSON formatado para criação de bug.",
            'advisor' => "Você é um consultant DevOps.

Analise a situação e forneça recomendações:
- ações sugeridas
- alertas importantes
- próximas verificações

Retorne JSON com recomendações.",
            default => "",
        };
    }
}