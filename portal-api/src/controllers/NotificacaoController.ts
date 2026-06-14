import type { NextFunction, Request, Response } from "express";
import type { NotificacaoRepository } from "../repositories/NotificacaoRepository";
import AppError from "../utils/AppError";

export class NotificacaoController {
  constructor(private readonly notificacoes: NotificacaoRepository) {}

  minhasNotificacoes = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { id, tipo } = req.user!;
      const notificacoes = await this.notificacoes.listarPorDestinatario(id, tipo);
      res.json({ notificacoes });
    } catch (e) { next(e); }
  };

  marcarLida = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id);
      if (!Number.isInteger(id) || id < 1) throw new AppError("ID inválido", 400);
      await this.notificacoes.marcarLida(id);
      res.json({ message: "Notificação marcada como lida" });
    } catch (e) { next(e); }
  };
}
