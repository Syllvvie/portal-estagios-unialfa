import type { NextFunction, Request, Response } from "express";
import { z } from "zod";
import type { AlunoService } from "../services/AlunoService";
import AppError from "../utils/AppError";

export class AlunoController {
  constructor(private readonly alunosService: AlunoService) {}

  private schemaRegistrar = z.object({
    ra: z.string({ message: "RA e obrigatorio" }).min(1).max(6, "RA deve ter no maximo 6 digitos").regex(/^\d+$/, "RA deve conter apenas numeros"),
    nome: z.string({ message: "Nome é obrigatório" }).min(2),
    email: z.string().email({ message: "E-mail inválido" }),
    senha: z.string().min(6, "Mínimo 6 caracteres"),
    telefone: z.string().optional(),
    curso: z.string().optional(),
    periodo: z.number().int().optional(),
  });

  private schemaAtualizar = z.object({
    nome: z.string().min(2).optional(),
    email: z.string().email().optional(),
    senha: z.string().min(6).optional(),
    telefone: z.string().optional(),
    curso: z.string().optional(),
    periodo: z.number().int().optional(),
  });

  registrar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const input = this.schemaRegistrar.parse(req.body);
      const aluno = await this.alunosService.registrar(input);
      res.status(201).json({ aluno });
    } catch (e) { next(e); }
  };

  listar = async (_req: Request, res: Response, next: NextFunction) => {
    try {
      const alunos = await this.alunosService.listar();
      res.json({ alunos });
    } catch (e) { next(e); }
  };

  buscarPorId = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      const aluno = await this.alunosService.buscarPorId(id);
      res.json({ aluno });
    } catch (e) { next(e); }
  };

  atualizar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      const dados = this.schemaAtualizar.parse(req.body);
      const aluno = await this.alunosService.atualizar(id, dados);
      res.json({ message: "Aluno atualizado com sucesso", aluno });
    } catch (e) { next(e); }
  };

  remover = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      await this.alunosService.remover(id);
      res.json({ message: "Aluno removido com sucesso" });
    } catch (e) { next(e); }
  };

  perfil = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = req.user!.id;
      const aluno = await this.alunosService.buscarPorId(id);
      res.json({ aluno });
    } catch (e) { next(e); }
  };

  trocarSenha = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = req.user!.id;
      const { nova_senha } = z.object({
        nova_senha: z.string().min(6, "Nova senha deve ter no mínimo 6 caracteres"),
      }).parse(req.body);
      const aluno = await this.alunosService.trocarSenha(id, nova_senha);
      res.json({ message: "Senha alterada com sucesso", aluno });
    } catch (e) { next(e); }
  };
}
