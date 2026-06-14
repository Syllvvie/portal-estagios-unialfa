import type { NextFunction, Request, Response } from "express";
import { z } from "zod";
import type { SessionService } from "../services/SessionService";

export class SessionController {
  constructor(private readonly sessionService: SessionService) {}

  loginAluno = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { ra, senha } = z.object({
      ra: z.string({ message: "RA e obrigatorio" }).max(6),
        senha: z.string({ message: "Senha é obrigatória" }),
      }).parse(req.body);
      const resultado = await this.sessionService.loginAluno(ra, senha);
      res.json(resultado);
    } catch (e) { next(e); }
  };

  loginEmpresa = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { email, senha } = z.object({
        email: z.string().email(),
        senha: z.string(),
      }).parse(req.body);
      const resultado = await this.sessionService.loginEmpresa(email, senha);
      res.json(resultado);
    } catch (e) { next(e); }
  };
}
