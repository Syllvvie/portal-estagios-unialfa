<?php

/**
 * Classe abstrata base para todas as entidades do domínio.
 * Aplica: Abstração, Encapsulamento, Polimorfismo (método toArray / __toString).
 */
abstract class EntidadeBase
{
    protected ?int $id = null;
    protected string $createdAt = '';

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $v): void { $this->createdAt = $v; }

    /**
     * Cada subclasse deve saber converter seus dados para array.
     * Polimorfismo: cada entidade implementa do seu jeito.
     */
    abstract public function toArray(): array;

    /**
     * Representação em string para debug / relatórios simples.
     */
    abstract public function __toString(): string;

    /**
     * Preenche a entidade a partir de um array vindo da API.
     * Herança: subclasses chamam parent::preencherBase() e complementam.
     */
    protected function preencherBase(array $dados): void
    {
        $this->id        = isset($dados['id'])         ? (int)$dados['id']      : null;
        $this->createdAt = $dados['created_at']        ?? '';
    }

    /**
     * Factory estática — cada subclasse sobrescreve para instanciar a si mesma.
     * Polimorfismo de fábrica.
     */
    public static function fromArray(array $dados): static
    {
        // @phpstan-ignore-next-line
        $obj = new static();
        $obj->preencherBase($dados);
        return $obj;
    }
}
