import type { Repository } from "typeorm";
import type { Aluno, AlunoPublico } from "../models/Aluno";

export type CriarAlunoInput = {
  ra: string;
  nome: string;
  email: string;
  senha: string;
  apto: boolean;
  ativo: boolean;
  telefone?: string | null;
  curso?: string | null;
  periodo?: number | null;
};

export interface IAlunoRepository {
  listarTodos(): Promise<AlunoPublico[]>;
  buscarPorId(id: number): Promise<Aluno | undefined>;
  buscarPorRa(ra: string): Promise<Aluno | undefined>;
  buscarPorEmail(email: string): Promise<Aluno | undefined>;
  criar(dados: CriarAlunoInput): Promise<AlunoPublico>;
  salvar(entidade: Aluno): Promise<AlunoPublico>;
  remover(id: number): Promise<boolean>;
}

function semSenha(a: Aluno): AlunoPublico {
  const { senha: _s, candidaturas: _c, ...rest } = a;
  return rest;
}

export class AlunoRepository implements IAlunoRepository {
  constructor(private readonly repo: Repository<Aluno>) {}

  async listarTodos(): Promise<AlunoPublico[]> {
    const rows = await this.repo.find({ order: { id: "ASC" } });
    return rows.map(semSenha);
  }

  async buscarPorId(id: number): Promise<Aluno | undefined> {
    const row = await this.repo.findOne({ where: { id } });
    return row ?? undefined;
  }

  async buscarPorRa(ra: string): Promise<Aluno | undefined> {
    const row = await this.repo.findOne({ where: { ra } });
    return row ?? undefined;
  }

  async buscarPorEmail(email: string): Promise<Aluno | undefined> {
    const row = await this.repo.findOne({ where: { email } });
    return row ?? undefined;
  }

  async criar(dados: CriarAlunoInput): Promise<AlunoPublico> {
    const ent = this.repo.create(dados);
    const salvo = await this.repo.save(ent);
    return semSenha(salvo);
  }

  async salvar(entidade: Aluno): Promise<AlunoPublico> {
    const salvo = await this.repo.save(entidade);
    return semSenha(salvo);
  }

  async remover(id: number): Promise<boolean> {
    const r = await this.repo.delete(id);
    return (r.affected ?? 0) > 0;
  }
}
