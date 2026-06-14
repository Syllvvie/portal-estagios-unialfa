import type { Repository } from "typeorm";
import type { Empresa, EmpresaPublica, StatusEmpresa } from "../models/Empresa";

export type CriarEmpresaInput = {
  nome: string;
  cnpj: string;
  email: string;
  senha: string;
  status: StatusEmpresa;
  telefone?: string | null;
  endereco?: string | null;
  area_atuacao?: string | null;
};

export interface IEmpresaRepository {
  listarTodas(): Promise<EmpresaPublica[]>;
  buscarPorId(id: number): Promise<Empresa | undefined>;
  buscarPorEmail(email: string): Promise<Empresa | undefined>;
  buscarPorCnpj(cnpj: string): Promise<Empresa | undefined>;
  criar(dados: CriarEmpresaInput): Promise<EmpresaPublica>;
  salvar(entidade: Empresa): Promise<EmpresaPublica>;
  remover(id: number): Promise<boolean>;
}

function semSenha(e: Empresa): EmpresaPublica {
  const { senha: _s, vagas: _v, ...rest } = e;
  return rest;
}

export class EmpresaRepository implements IEmpresaRepository {
  constructor(private readonly repo: Repository<Empresa>) {}

  async listarTodas(): Promise<EmpresaPublica[]> {
    const rows = await this.repo.find({ order: { id: "ASC" } });
    return rows.map(semSenha);
  }

  async buscarPorId(id: number): Promise<Empresa | undefined> {
    const row = await this.repo.findOne({ where: { id } });
    return row ?? undefined;
  }

  async buscarPorEmail(email: string): Promise<Empresa | undefined> {
    const row = await this.repo.findOne({ where: { email } });
    return row ?? undefined;
  }

  async buscarPorCnpj(cnpj: string): Promise<Empresa | undefined> {
    const row = await this.repo.findOne({ where: { cnpj } });
    return row ?? undefined;
  }

  async criar(dados: CriarEmpresaInput): Promise<EmpresaPublica> {
    const ent = this.repo.create(dados);
    const salvo = await this.repo.save(ent);
    return semSenha(salvo);
  }

  async salvar(entidade: Empresa): Promise<EmpresaPublica> {
    const salvo = await this.repo.save(entidade);
    return semSenha(salvo);
  }

  async remover(id: number): Promise<boolean> {
    const r = await this.repo.delete(id);
    return (r.affected ?? 0) > 0;
  }
}
