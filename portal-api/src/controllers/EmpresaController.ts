import type { NextFunction, Request, Response } from "express";
import { z } from "zod";
import type { EmpresaService } from "../services/EmpresaService";
import AppError from "../utils/AppError";

export class EmpresaController {
  constructor(private readonly empresasService: EmpresaService) {}

  private schemaRegistrar = z.object({
    nome: z.string().min(2),
    cnpj: z.string().min(14),
    email: z.string().email(),
    senha: z.string().min(6),
    telefone: z.string().optional(),
    endereco: z.string().optional(),
    area_atuacao: z.string().optional(),
  });

  private schemaAtualizar = z.object({
    nome: z.string().min(2).optional(),
    email: z.string().email().optional(),
    telefone: z.string().optional(),
    endereco: z.string().optional(),
    area_atuacao: z.string().optional(),
  });

  registrar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const input = this.schemaRegistrar.parse(req.body);
      const empresa = await this.empresasService.registrar(input);
      res.status(201).json({ empresa });
    } catch (e) { next(e); }
  };

  listar = async (_req: Request, res: Response, next: NextFunction) => {
    try {
      const empresas = await this.empresasService.listar();
      res.json({ empresas });
    } catch (e) { next(e); }
  };

  buscarPorId = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      const empresa = await this.empresasService.buscarPorId(id);
      res.json({ empresa });
    } catch (e) { next(e); }
  };

  atualizar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      const dados = this.schemaAtualizar.parse(req.body);
      const empresa = await this.empresasService.atualizar(id, dados);
      res.json({ message: "Empresa atualizada com sucesso", empresa });
    } catch (e) { next(e); }
  };

  atualizarStatus = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      const { status } = z.object({
        status: z.enum(["pendente", "aprovada", "bloqueada"]),
      }).parse(req.body);
      const empresa = await this.empresasService.atualizarStatus(id, status);
      res.json({ message: "Status atualizado", empresa });
    } catch (e) { next(e); }
  };

  remover = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      await this.empresasService.remover(id);
      res.json({ message: "Empresa removida com sucesso" });
    } catch (e) { next(e); }
  };

  perfil = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = req.user!.id;
      const empresa = await this.empresasService.buscarPorId(id);
      res.json({ empresa });
    } catch (e) { next(e); }
  };
}
