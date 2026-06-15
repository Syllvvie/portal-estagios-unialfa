<?php

require_once __DIR__ . '/EntidadeBase.php';
require_once __DIR__ . '/Empresa.php';

/**
 * Entidade Vaga.
 * Herança: estende EntidadeBase.
 * Composição: contém um objeto Empresa.
 */
class Vaga extends EntidadeBase
{
    private string   $titulo           = '';
    private string   $descricao        = '';
    private ?string  $area             = null;
    private ?string  $requisitos       = null;
    private ?float   $bolsa            = null;
    private ?string  $local            = null;
    private ?int     $cargaHoraria     = null;
    private ?string  $dataEncerramento = null;
    private bool     $ativa            = true;
    private int      $empresaId        = 0;
    private ?Empresa $empresa          = null;

    // ── Getters ──────────────────────────────────────────────────────────────

    public function getTitulo(): string          { return $this->titulo; }
    public function getDescricao(): string       { return $this->descricao; }
    public function getArea(): ?string           { return $this->area; }
    public function getRequisitos(): ?string     { return $this->requisitos; }
    public function getBolsa(): ?float           { return $this->bolsa; }
    public function getLocal(): ?string          { return $this->local; }
    public function getCargaHoraria(): ?int      { return $this->cargaHoraria; }
    public function getDataEncerramento(): ?string { return $this->dataEncerramento; }
    public function isAtiva(): bool              { return $this->ativa; }
    public function getEmpresaId(): int          { return $this->empresaId; }
    public function getEmpresa(): ?Empresa       { return $this->empresa; }

    // ── Setters ──────────────────────────────────────────────────────────────

    public function setTitulo(string $v): void           { $this->titulo = $v; }
    public function setDescricao(string $v): void        { $this->descricao = $v; }
    public function setArea(?string $v): void            { $this->area = $v; }
    public function setRequisitos(?string $v): void      { $this->requisitos = $v; }
    public function setBolsa(?float $v): void            { $this->bolsa = $v; }
    public function setLocal(?string $v): void           { $this->local = $v; }
    public function setCargaHoraria(?int $v): void       { $this->cargaHoraria = $v; }
    public function setDataEncerramento(?string $v): void { $this->dataEncerramento = $v; }
    public function setAtiva(bool $v): void              { $this->ativa = $v; }
    public function setEmpresaId(int $v): void           { $this->empresaId = $v; }
    public function setEmpresa(?Empresa $v): void        { $this->empresa = $v; }

    // ── Herança: sobrescreve preencherBase ───────────────────────────────────

    protected function preencherBase(array $dados): void
    {
        parent::preencherBase($dados);
        $this->titulo           = $dados['titulo']           ?? '';
        $this->descricao        = $dados['descricao']        ?? '';
        $this->area             = $dados['area']             ?? null;
        $this->requisitos       = $dados['requisitos']       ?? null;
        $this->bolsa            = isset($dados['bolsa'])     ? (float)$dados['bolsa'] : null;
        $this->local            = $dados['local']            ?? null;
        $this->cargaHoraria     = isset($dados['carga_horaria']) ? (int)$dados['carga_horaria'] : null;
        $this->dataEncerramento = $dados['data_encerramento'] ?? null;
        $this->ativa            = (bool)($dados['ativa']     ?? true);
        $this->empresaId        = isset($dados['empresa_id']) ? (int)$dados['empresa_id'] : 0;

        // Hidrata a Empresa aninhada se vier junto
        if (!empty($dados['empresa']) && is_array($dados['empresa'])) {
            $this->empresa = Empresa::fromArray($dados['empresa']);
        }
    }

    // ── Polimorfismo ──────────────────────────────────────────────────────────

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'titulo'            => $this->titulo,
            'descricao'         => $this->descricao,
            'area'              => $this->area,
            'requisitos'        => $this->requisitos,
            'bolsa'             => $this->bolsa,
            'local'             => $this->local,
            'carga_horaria'     => $this->cargaHoraria,
            'data_encerramento' => $this->dataEncerramento,
            'ativa'             => $this->ativa,
            'empresa_id'        => $this->empresaId,
            'empresa'           => $this->empresa?->toArray(),
            'created_at'        => $this->createdAt,
        ];
    }

    public function __toString(): string
    {
        $emp = $this->empresa ? " — {$this->empresa->getNome()}" : '';
        return "{$this->titulo}{$emp}";
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getBolsaFormatada(): string
    {
        return $this->bolsa !== null
            ? 'R$ ' . number_format($this->bolsa, 2, ',', '.')
            : 'Não informada';
    }

    public function getCargaHorariaLabel(): string
    {
        return $this->cargaHoraria !== null ? "{$this->cargaHoraria}h/semana" : 'Não informada';
    }

    public function getEmpresaNome(): string
    {
        return $this->empresa?->getNome() ?? 'Empresa não informada';
    }

    public function getStatusLabel(): string
    {
        return $this->ativa ? 'Ativa' : 'Encerrada';
    }
}
