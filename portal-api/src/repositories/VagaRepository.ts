import type { Repository } from "typeorm";
import type { Vaga } from "../models/Vaga";

export interface IVagaRepository {
  listar(apenasAtivas?: boolean): Promise<Vaga[]>;
  listarPorEmpresa(empresa_id: number): Promise<Vaga[]>;
  buscarPorId(id: number): Promise<Vaga | undefined>;
  criar(dados: Partial<Vaga>): Promise<Vaga>;
  salvar(entidade: Vaga): Promise<Vaga>;
  remover(id: number): Promise<boolean>;
}

export class VagaRepository implements IVagaRepository {
  constructor(private readonly repo: Repository<Vaga>) {}

  async listar(apenasAtivas = false): Promise<Vaga[]> {
    const where: any = {};
    if (apenasAtivas) where.ativa = true;
    return this.repo.find({
      where,
      relations: ["empresa"],
      order: { id: "DESC" },
    });
  }

  async listarPorEmpresa(empresa_id: number): Promise<Vaga[]> {
    return this.repo.find({
      where: { empresa_id },
      order: { id: "DESC" },
    });
  }

  async buscarPorId(id: number): Promise<Vaga | undefined> {
    const row = await this.repo.findOne({
      where: { id },
      relations: ["empresa"],
    });
    return row ?? undefined;
  }

  async criar(dados: Partial<Vaga>): Promise<Vaga> {
    const ent = this.repo.create(dados);
    return this.repo.save(ent);
  }

  async salvar(entidade: Vaga): Promise<Vaga> {
    return this.repo.save(entidade);
  }

  async remover(id: number): Promise<boolean> {
    const r = await this.repo.delete(id);
    return (r.affected ?? 0) > 0;
  }
}
