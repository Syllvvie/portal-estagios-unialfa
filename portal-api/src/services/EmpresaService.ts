import { hash } from "bcrypt";
import type { IEmpresaRepository, CriarEmpresaInput } from "../repositories/EmpresaRepository";
import type { EmpresaPublica, StatusEmpresa } from "../models/Empresa";
import AppError from "../utils/AppError";

export class EmpresaService {
  constructor(private readonly empresas: IEmpresaRepository) {}

  async listar(): Promise<EmpresaPublica[]> {
    return this.empresas.listarTodas();
  }

  async buscarPorId(id: number): Promise<EmpresaPublica> {
    const e = await this.empresas.buscarPorId(id);
    if (!e) throw new AppError("Empresa não encontrada", 404);
    const { senha: _s, vagas: _v, ...pub } = e;
    return pub;
  }

  async registrar(input: {
    nome: string;
    cnpj: string;
    email: string;
    senha: string;
    telefone?: string;
    endereco?: string;
    area_atuacao?: string;
  }): Promise<EmpresaPublica> {
    if (await this.empresas.buscarPorEmail(input.email)) {
      throw new AppError("E-mail já cadastrado", 409);
    }
    if (await this.empresas.buscarPorCnpj(input.cnpj)) {
      throw new AppError("CNPJ já cadastrado", 409);
    }
    const senha = await hash(input.senha, 8);
    return this.empresas.criar({ ...input, senha, status: "pendente" });
  }

  async atualizar(id: number, dados: Partial<{ nome: string; email: string; telefone: string; endereco: string; area_atuacao: string }>): Promise<EmpresaPublica> {
    const empresa = await this.empresas.buscarPorId(id);
    if (!empresa) throw new AppError("Empresa não encontrada", 404);

    if (dados.email && dados.email !== empresa.email) {
      if (await this.empresas.buscarPorEmail(dados.email)) {
        throw new AppError("E-mail já cadastrado", 409);
      }
    }

    Object.assign(empresa, dados);
    return this.empresas.salvar(empresa);
  }

  async atualizarStatus(id: number, status: StatusEmpresa): Promise<EmpresaPublica> {
    const empresa = await this.empresas.buscarPorId(id);
    if (!empresa) throw new AppError("Empresa não encontrada", 404);
    empresa.status = status;
    return this.empresas.salvar(empresa);
  }

  async remover(id: number): Promise<void> {
    const ok = await this.empresas.remover(id);
    if (!ok) throw new AppError("Empresa não encontrada", 404);
  }
}
