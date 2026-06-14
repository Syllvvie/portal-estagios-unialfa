import type { ICandidaturaRepository } from "../repositories/CandidaturaRepository";
import type { IVagaRepository } from "../repositories/VagaRepository";
import type { IAlunoRepository } from "../repositories/AlunoRepository";
import type { NotificacaoRepository } from "../repositories/NotificacaoRepository";
import type { Candidatura, StatusCandidatura } from "../models/Candidatura";
import AppError from "../utils/AppError";

export class CandidaturaService {
  constructor(
    private readonly candidaturas: ICandidaturaRepository,
    private readonly vagas: IVagaRepository,
    private readonly alunos: IAlunoRepository,
    private readonly notificacoes: NotificacaoRepository
  ) {}

  async listar(): Promise<Candidatura[]> {
    return this.candidaturas.listar();
  }

  async listarPorAluno(aluno_id: number): Promise<Candidatura[]> {
    return this.candidaturas.listarPorAluno(aluno_id);
  }

  async listarPorVaga(vaga_id: number): Promise<Candidatura[]> {
    return this.candidaturas.listarPorVaga(vaga_id);
  }

  async buscarPorId(id: number): Promise<Candidatura> {
    const c = await this.candidaturas.buscarPorId(id);
    if (!c) throw new AppError("Candidatura não encontrada", 404);
    return c;
  }

  async candidatar(aluno_id: number, vaga_id: number, carta_apresentacao?: string): Promise<Candidatura> {
    // Verifica se aluno existe e está apto
    const aluno = await this.alunos.buscarPorId(aluno_id);
    if (!aluno) throw new AppError("Aluno não encontrado", 404);
    if (!aluno.apto) throw new AppError("Aluno não está apto para se candidatar", 403);
    if (!aluno.ativo) throw new AppError("Aluno inativo", 403);

    // Verifica se vaga existe e está ativa
    const vaga = await this.vagas.buscarPorId(vaga_id);
    if (!vaga) throw new AppError("Vaga não encontrada", 404);
    if (!vaga.ativa) throw new AppError("Vaga não está mais disponível", 400);

    // Verifica candidatura duplicada
    const duplicata = await this.candidaturas.buscarDuplicata(aluno_id, vaga_id);
    if (duplicata) throw new AppError("Você já se candidatou a esta vaga", 409);

    const candidatura = await this.candidaturas.criar({
      aluno_id,
      vaga_id,
      carta_apresentacao,
      status: "pendente",
    });

    // Notifica empresa
    await this.notificacoes.criar({
      destinatario_id: vaga.empresa_id,
      tipo_destinatario: "empresa",
      titulo: "Nova candidatura recebida",
      mensagem: `${aluno.nome} se candidatou à vaga "${vaga.titulo}"`,
    });

    return candidatura;
  }

  async atualizarStatus(id: number, empresa_id: number, status: StatusCandidatura, observacao?: string): Promise<Candidatura> {
    const candidatura = await this.candidaturas.buscarPorId(id);
    if (!candidatura) throw new AppError("Candidatura não encontrada", 404);

    // Verifica se a vaga pertence à empresa
    const vaga = await this.vagas.buscarPorId(candidatura.vaga_id);
    if (!vaga || vaga.empresa_id !== empresa_id) {
      throw new AppError("Acesso negado", 403);
    }

    const statusAnterior = candidatura.status;
    candidatura.status = status;
    if (observacao !== undefined) candidatura.observacao = observacao;

    const atualizada = await this.candidaturas.salvar(candidatura);

    // Notifica aluno sobre mudança de status
    if (statusAnterior !== status) {
      const statusLabel: Record<StatusCandidatura, string> = {
        pendente: "Pendente",
        em_analise: "Em Análise",
        aprovada: "Aprovada",
        reprovada: "Reprovada",
        cancelada: "Cancelada",
      };
      await this.notificacoes.criar({
        destinatario_id: candidatura.aluno_id,
        tipo_destinatario: "aluno",
        titulo: `Candidatura ${statusLabel[status]}`,
        mensagem: `Sua candidatura à vaga "${vaga.titulo}" foi atualizada para: ${statusLabel[status]}${observacao ? ". " + observacao : ""}`,
      });
    }

    return atualizada;
  }

  async cancelar(id: number, aluno_id: number): Promise<Candidatura> {
    const candidatura = await this.candidaturas.buscarPorId(id);
    if (!candidatura) throw new AppError("Candidatura não encontrada", 404);
    if (candidatura.aluno_id !== aluno_id) throw new AppError("Acesso negado", 403);
    if (candidatura.status !== "pendente") {
      throw new AppError("Só é possível cancelar candidaturas pendentes", 400);
    }
    candidatura.status = "cancelada";
    return this.candidaturas.salvar(candidatura);
  }
}
