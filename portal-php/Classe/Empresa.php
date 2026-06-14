<?php

require_once __DIR__ . '/EntidadeBase.php';

/**
 * Entidade Empresa.
 * Herança: estende EntidadeBase.
 */
class Empresa extends EntidadeBase
{
    private string  $nome       = '';
    private string  $cnpj       = '';
    private string  $email      = '';
    private ?string $telefone   = null;
    private ?string $endereco   = null;
    private ?string $areaAtuacao = null;
    private string  $status     = 'pendente'; // pendente | aprovada | bloqueada

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getNome(): string       { return $this->nome; }
    public function getCnpj(): string       { return $this->cnpj; }
    public function getEmail(): string      { return $this->email; }
    public function getTelefone(): ?string  { return $this->telefone; }
    public function getEndereco(): ?string  { return $this->endereco; }
    public function getAreaAtuacao(): ?string { return $this->areaAtuacao; }
    public function getStatus(): string     { return $this->status; }

    // ── Setters ──────────────────────────────────────────────────────────────

    public function setNome(string $v): void        { $this->nome = $v; }
    public function setCnpj(string $v): void        { $this->cnpj = $v; }
    public function setEmail(string $v): void       { $this->email = $v; }
    public function setTelefone(?string $v): void   { $this->telefone = $v; }
    public function setEndereco(?string $v): void   { $this->endereco = $v; }
    public function setAreaAtuacao(?string $v): void { $this->areaAtuacao = $v; }
    public function setStatus(string $v): void      { $this->status = $v; }

    // ── Herança: sobrescreve preencherBase ───────────────────────────────────

    protected function preencherBase(array $dados): void
    {
        parent::preencherBase($dados);
        $this->nome        = $dados['nome']         ?? '';
        $this->cnpj        = $dados['cnpj']         ?? '';
        $this->email       = $dados['email']        ?? '';
        $this->telefone    = $dados['telefone']     ?? null;
        $this->endereco    = $dados['endereco']     ?? null;
        $this->areaAtuacao = $dados['area_atuacao'] ?? null;
        $this->status      = $dados['status']       ?? 'pendente';
    }

    // ── Polimorfismo ──────────────────────────────────────────────────────────

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'nome'         => $this->nome,
            'cnpj'         => $this->cnpj,
            'email'        => $this->email,
            'telefone'     => $this->telefone,
            'endereco'     => $this->endereco,
            'area_atuacao' => $this->areaAtuacao,
            'status'       => $this->status,
            'created_at'   => $this->createdAt,
        ];
    }

    public function __toString(): string
    {
        return "{$this->nome} ({$this->cnpj})";
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isAprovada(): bool  { return $this->status === 'aprovada'; }
    public function isBloqueada(): bool { return $this->status === 'bloqueada'; }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'aprovada'  => 'Aprovada',
            'bloqueada' => 'Bloqueada',
            default     => 'Pendente',
        };
    }

    public function getInicialNome(): string
    {
        return mb_strtoupper(mb_substr($this->nome, 0, 1));
    }
}
