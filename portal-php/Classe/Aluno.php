<?php

require_once __DIR__ . '/EntidadeBase.php';

/**
 * Entidade Aluno.
 * Herança: estende EntidadeBase.
 * Encapsulamento: todos os atributos privados com getters/setters.
 */
class Aluno extends EntidadeBase
{
    private string  $ra       = '';
    private string  $nome     = '';
    private string  $email    = '';
    private ?string $telefone = null;
    private ?string $curso    = null;
    private ?int    $periodo  = null;
    private bool    $apto     = true;
    private bool    $ativo    = true;
    private bool    $primeiroAcesso = true;

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getRa(): string      { return $this->ra; }
    public function getNome(): string    { return $this->nome; }
    public function getEmail(): string   { return $this->email; }
    public function getTelefone(): ?string { return $this->telefone; }
    public function getCurso(): ?string  { return $this->curso; }
    public function getPeriodo(): ?int   { return $this->periodo; }
    public function isApto(): bool       { return $this->apto; }
    public function isAtivo(): bool      { return $this->ativo; }
    public function isPrimeiroAcesso(): bool { return $this->primeiroAcesso; }

    // ── Setters ──────────────────────────────────────────────────────────────

    public function setRa(string $v): void       { $this->ra = $v; }
    public function setNome(string $v): void     { $this->nome = $v; }
    public function setEmail(string $v): void    { $this->email = $v; }
    public function setTelefone(?string $v): void { $this->telefone = $v; }
    public function setCurso(?string $v): void   { $this->curso = $v; }
    public function setPeriodo(?int $v): void    { $this->periodo = $v; }
    public function setApto(bool $v): void       { $this->apto = $v; }
    public function setAtivo(bool $v): void      { $this->ativo = $v; }
    public function setPrimeiroAcesso(bool $v): void { $this->primeiroAcesso = $v; }

    // ── Herança: sobrescreve preencherBase ───────────────────────────────────

    protected function preencherBase(array $dados): void
    {
        parent::preencherBase($dados);
        $this->ra             = $dados['ra']              ?? '';
        $this->nome           = $dados['nome']            ?? '';
        $this->email          = $dados['email']           ?? '';
        $this->telefone       = $dados['telefone']        ?? null;
        $this->curso          = $dados['curso']           ?? null;
        $this->periodo        = isset($dados['periodo'])  ? (int)$dados['periodo'] : null;
        $this->apto           = (bool)($dados['apto']     ?? true);
        $this->ativo          = (bool)($dados['ativo']    ?? true);
        $this->primeiroAcesso = (bool)($dados['primeiro_acesso'] ?? true);
    }

    // ── Polimorfismo: implementa toArray e __toString ─────────────────────────

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'ra'              => $this->ra,
            'nome'            => $this->nome,
            'email'           => $this->email,
            'telefone'        => $this->telefone,
            'curso'           => $this->curso,
            'periodo'         => $this->periodo,
            'apto'            => $this->apto,
            'ativo'           => $this->ativo,
            'primeiro_acesso' => $this->primeiroAcesso,
            'created_at'      => $this->createdAt,
        ];
    }

    public function __toString(): string
    {
        return "{$this->nome} (RA: {$this->ra})";
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    public function getPeriodoLabel(): string
    {
        return $this->periodo !== null ? "{$this->periodo}º período" : 'Não informado';
    }

    public function getStatusLabel(): string
    {
        if (!$this->ativo) return 'Inativo';
        return $this->apto ? 'Apto' : 'Não apto';
    }
}
