import type { NextFunction, Request, Response } from "express";
import { z } from "zod";
import type { VagaService } from "../services/VagaService";
import AppError from "../utils/AppError";

export class VagaController {
  constructor(private readonly vagasService: VagaService) {}

  private schemaVaga = z.object({
    titulo: z.string().min(3),
    descricao: z.string().min(10),
    area: z.string().optional(),
    requisitos: z.string().optional(),
    bolsa: z.number().optional(),
    local: z.string().optional(),
    carga_horaria: z.number().int().optional(),
    data_encerramento: z.string().optional(),
    ativa: z.boolean().optional(),
  });

  listar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const apenasAtivas = req.query.ativas === "true";
      const vagas = await this.vagasService.listar(apenasAtivas);
      res.json({ vagas });
    } catch (e) { next(e); }
  };

  listarPorEmpresa = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const empresa_id = Number(req.params.empresa_id);
      const vagas = await this.vagasService.listarPorEmpresa(empresa_id);
      res.json({ vagas });
    } catch (e) { next(e); }
  };

  minhasVagas = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const empresa_id = req.user!.id;
      const vagas = await this.vagasService.listarPorEmpresa(empresa_id);
      res.json({ vagas });
    } catch (e) { next(e); }
  };

  buscarPorId = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      const vaga = await this.vagasService.buscarPorId(id);
      res.json({ vaga });
    } catch (e) { next(e); }
  };

  criar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "empresa") throw new AppError("Apenas empresas podem criar vagas", 403);
      const dados = this.schemaVaga.parse(req.body);
      const vaga = await this.vagasService.criar(req.user.id, dados as any);
      res.status(201).json({ vaga });
    } catch (e) { next(e); }
  };

  atualizar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "empresa") throw new AppError("Acesso negado", 403);
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      const dados = this.schemaVaga.partial().parse(req.body);
      const vaga = await this.vagasService.atualizar(id, req.user.id, dados as any);
      res.json({ message: "Vaga atualizada com sucesso", vaga });
    } catch (e) { next(e); }
  };

  remover = async (req: Request, res: Response, next: NextFunction) => {
    try {
      if (req.user?.tipo !== "empresa") throw new AppError("Acesso negado", 403);
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      await this.vagasService.remover(id, req.user.id);
      res.json({ message: "Vaga removida com sucesso" });
    } catch (e) { next(e); }
  };
}
