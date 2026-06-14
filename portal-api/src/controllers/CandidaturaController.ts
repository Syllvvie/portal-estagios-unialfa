import type { NextFunction, Request, Response } from "express";
import { z } from "zod";
import type { CandidaturaService } from "../services/CandidaturaService";
import AppError from "../utils/AppError";

export class CandidaturaController {
  constructor(private readonly candidaturasService: CandidaturaService) {}

  listar = async (_req: Request, res: Response, next: NextFunction) => {
    try {
      const candidaturas = await this.candidaturasService.listar();
      res.json({ candidaturas });
    } catch (e) { next(e); }
  };

  minhasCandidaturas = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "aluno") throw new AppError("Acesso negado", 403);
      const candidaturas = await this.candidaturasService.listarPorAluno(req.user.id);
      res.json({ candidaturas });
    } catch (e) { next(e); }
  };

  candidatosDaVaga = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const vaga_id = Number(req.params.vaga_id);
      const candidaturas = await this.candidaturasService.listarPorVaga(vaga_id);
      res.json({ candidaturas });
    } catch (e) { next(e); }
  };

  candidatar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "aluno") throw new AppError("Apenas alunos podem se candidatar", 403);
      const { vaga_id, carta_apresentacao } = z.object({
        vaga_id: z.number().int().positive(),
        carta_apresentacao: z.string().optional(),
      }).parse(req.body);
      const candidatura = await this.candidaturasService.candidatar(req.user.id, vaga_id, carta_apresentacao);
      res.status(201).json({ candidatura });
    } catch (e) { next(e); }
  };

  atualizarStatus = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "empresa") throw new AppError("Apenas empresas podem alterar o status", 403);
      const id = Number(req.params.id);
      const { status, observacao } = z.object({
        status: z.enum(["pendente", "em_analise", "aprovada", "reprovada", "cancelada"]),
        observacao: z.string().optional(),
      }).parse(req.body);
      const candidatura = await this.candidaturasService.atualizarStatus(id, req.user.id, status, observacao);
      res.json({ message: "Status atualizado", candidatura });
    } catch (e) { next(e); }
  };

  cancelar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "aluno") throw new AppError("Acesso negado", 403);
      const id = Number(req.params.id);
      const candidatura = await this.candidaturasService.cancelar(id, req.user.id);
      res.json({ message: "Candidatura cancelada", candidatura });
    } catch (e) { next(e); }
  };
}
