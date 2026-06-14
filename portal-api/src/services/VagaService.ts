import type { IVagaRepository } from "../repositories/VagaRepository";
import type { IEmpresaRepository } from "../repositories/EmpresaRepository";
import type { Vaga } from "../models/Vaga";
import AppError from "../utils/AppError";

export class VagaService {
  constructor(
    private readonly vagas: IVagaRepository,
    private readonly empresas: IEmpresaRepository
  ) {}

  async listar(apenasAtivas = false): Promise<Vaga[]> {
    return this.vagas.listar(apenasAtivas);
  }

  async listarPorEmpresa(empresa_id: number): Promise<Vaga[]> {
    return this.vagas.listarPorEmpresa(empresa_id);
  }

  async buscarPorId(id: number): Promise<Vaga> {
    const v = await this.vagas.buscarPorId(id);
    if (!v) throw new AppError("Vaga não encontrada", 404);
    return v;
  }

  async criar(empresa_id: number, dados: Partial<Vaga>): Promise<Vaga> {
    const empresa = await this.empresas.buscarPorId(empresa_id);
    if (!empresa) throw new AppError("Empresa não encontrada", 404);
    if (empresa.status !== "aprovada") {
      throw new AppError("Empresa não aprovada para cadastrar vagas", 403);
    }
    return this.vagas.criar({ ...dados, empresa_id, ativa: true });
  }

  async atualizar(id: number, empresa_id: number, dados: Partial<Vaga>): Promise<Vaga> {
    const vaga = await this.vagas.buscarPorId(id);
    if (!vaga) throw new AppError("Vaga não encontrada", 404);
    if (vaga.empresa_id !== empresa_id) {
      throw new AppError("Acesso negado", 403);
    }
    Object.assign(vaga, dados);
    return this.vagas.salvar(vaga);
  }

  async remover(id: number, empresa_id: number): Promise<void> {
    const vaga = await this.vagas.buscarPorId(id);
    if (!vaga) throw new AppError("Vaga não encontrada", 404);
    if (vaga.empresa_id !== empresa_id) {
      throw new AppError("Acesso negado", 403);
    }
    await this.vagas.remover(id);
  }
}
