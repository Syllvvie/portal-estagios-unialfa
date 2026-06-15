<?php

require_once __DIR__ . '/EntidadeBase.php';
require_once __DIR__ . '/Aluno.php';
require_once __DIR__ . '/Vaga.php';

/**
 * Entidade Candidatura.
 * Herança: estende EntidadeBase.
 * Composição: contém Aluno e Vaga.
 * Polimorfismo: toArray e __toString com comportamento próprio.
 */
class Candidatura extends EntidadeBase
{
    // Valores possíveis de status (constantes de domínio)
    public const STATUS_PENDENTE   = 'pendente';
    public const STATUS_EM_ANALISE = 'em_analise';
    public const STATUS_APROVADA   = 'aprovada';
    public const STATUS_REPROVADA  = 'reprovada';
    public const STATUS_CANCELADA  = 'cancelada';

    private int     $alunoId           = 0;
    private int     $vagaId            = 0;
    private string  $status            = self::STATUS_PENDENTE;
    private ?string $cartaApresentacao = null;
    private ?string $observacao        = null;
    private ?Aluno  $aluno             = null;
    private ?Vaga   $vaga              = null;

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getAlunoId(): int             { return $this->alunoId; }
    public function getVagaId(): int              { return $this->vagaId; }
    public function getStatus(): string           { return $this->status; }
    public function getCartaApresentacao(): ?string { return $this->cartaApresentacao; }
    public function getObservacao(): ?string      { return $this->observacao; }
    public function getAluno(): ?Aluno            { return $this->aluno; }
    public function getVaga(): ?Vaga              { return $this->vaga; }

    // ── Setters ──────────────────────────────────────────────────────────────

    public function setAlunoId(int $v): void              { $this->alunoId = $v; }
    public function setVagaId(int $v): void               { $this->vagaId = $v; }
    public function setStatus(string $v): void            { $this->status = $v; }
    public function setCartaApresentacao(?string $v): void { $this->cartaApresentacao = $v; }
    public function setObservacao(?string $v): void       { $this->observacao = $v; }
    public function setAluno(?Aluno $v): void             { $this->aluno = $v; }
    public function setVaga(?Vaga $v): void               { $this->vaga = $v; }

    // ── Herança: sobrescreve preencherBase ───────────────────────────────────

    protected function preencherBase(array $dados): void
    {
        parent::preencherBase($dados);
        $this->alunoId           = isset($dados['aluno_id'])  ? (int)$dados['aluno_id']  : 0;
        $this->vagaId            = isset($dados['vaga_id'])   ? (int)$dados['vaga_id']   : 0;
        $this->status            = $dados['status']           ?? self::STATUS_PENDENTE;
        $this->cartaApresentacao = $dados['carta_apresentacao'] ?? null;
        $this->observacao        = $dados['observacao']       ?? null;

        if (!empty($dados['aluno']) && is_array($dados['aluno'])) {
            $this->aluno = Aluno::fromArray($dados['aluno']);
        }
        if (!empty($dados['vaga']) && is_array($dados['vaga'])) {
            $this->vaga = Vaga::fromArray($dados['vaga']);
        }
    }

    // ── Polimorfismo ──────────────────────────────────────────────────────────

    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'aluno_id'            => $this->alunoId,
            'vaga_id'             => $this->vagaId,
            'status'              => $this->status,
            'carta_apresentacao'  => $this->cartaApresentacao,
            'observacao'          => $this->observacao,
            'aluno'               => $this->aluno?->toArray(),
            'vaga'                => $this->vaga?->toArray(),
            'created_at'          => $this->createdAt,
        ];
    }

    public function __toString(): string
    {
        $aluno = $this->aluno ? $this->aluno->getNome() : "Aluno #{$this->alunoId}";
        $vaga  = $this->vaga  ? $this->vaga->getTitulo() : "Vaga #{$this->vagaId}";
        return "{$aluno} → {$vaga} [{$this->getStatusLabel()}]";
    }

    // ── Helpers de negócio ────────────────────────────────────────────────────

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDENTE   => 'Pendente',
            self::STATUS_EM_ANALISE => 'Em Análise',
            self::STATUS_APROVADA   => 'Aprovada',
            self::STATUS_REPROVADA  => 'Não selecionado',
            self::STATUS_CANCELADA  => 'Cancelada',
            default                 => $this->status,
        };
    }

    public function getStatusBadge(): array
    {
        return match($this->status) {
            self::STATUS_PENDENTE   => ['bg' => '#e0f2fe', 'cor' => '#0284c7'],
            self::STATUS_EM_ANALISE => ['bg' => '#fef9c3', 'cor' => '#a16207'],
            self::STATUS_APROVADA   => ['bg' => '#dcfce7', 'cor' => '#15803d'],
            self::STATUS_REPROVADA  => ['bg' => '#fee2e2', 'cor' => '#b91c1c'],
            self::STATUS_CANCELADA  => ['bg' => '#e2e3e5', 'cor' => '#495057'],
            default                 => ['bg' => '#f3f4f6', 'cor' => '#6b7280'],
        };
    }

    public function isPendente(): bool  { return $this->status === self::STATUS_PENDENTE; }
    public function isAprovada(): bool  { return $this->status === self::STATUS_APROVADA; }
    public function isCancelada(): bool { return $this->status === self::STATUS_CANCELADA; }

    public function getDataFormatada(): string
    {
        if (empty($this->createdAt)) return 'N/A';
        return date('d/m/Y', strtotime($this->createdAt));
    }
}
