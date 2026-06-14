import type { Repository } from "typeorm";
import type { Candidatura } from "../models/Candidatura";

export interface ICandidaturaRepository {
  listar(): Promise<Candidatura[]>;
  listarPorAluno(aluno_id: number): Promise<Candidatura[]>;
  listarPorVaga(vaga_id: number): Promise<Candidatura[]>;
  buscarPorId(id: number): Promise<Candidatura | undefined>;
  buscarDuplicata(aluno_id: number, vaga_id: number): Promise<Candidatura | undefined>;
  criar(dados: Partial<Candidatura>): Promise<Candidatura>;
  salvar(entidade: Candidatura): Promise<Candidatura>;
}

export class CandidaturaRepository implements ICandidaturaRepository {
  constructor(private readonly repo: Repository<Candidatura>) {}

  async listar(): Promise<Candidatura[]> {
    return this.repo.find({
      relations: ["aluno", "vaga", "vaga.empresa"],
      order: { id: "DESC" },
    });
  }

  async listarPorAluno(aluno_id: number): Promise<Candidatura[]> {
    return this.repo.find({
      where: { aluno_id },
      relations: ["vaga", "vaga.empresa"],
      order: { id: "DESC" },
    });
  }

  async listarPorVaga(vaga_id: number): Promise<Candidatura[]> {
    return this.repo.find({
      where: { vaga_id },
      relations: ["aluno"],
      order: { id: "DESC" },
    });
  }

  async buscarPorId(id: number): Promise<Candidatura | undefined> {
    const row = await this.repo.findOne({
      where: { id },
      relations: ["aluno", "vaga", "vaga.empresa"],
    });
    return row ?? undefined;
  }

  async buscarDuplicata(aluno_id: number, vaga_id: number): Promise<Candidatura | undefined> {
    const row = await this.repo.findOne({ where: { aluno_id, vaga_id } });
    return row ?? undefined;
  }

  async criar(dados: Partial<Candidatura>): Promise<Candidatura> {
    const ent = this.repo.create(dados);
    return this.repo.save(ent);
  }

  async salvar(entidade: Candidatura): Promise<Candidatura> {
    return this.repo.save(entidade);
  }
}
