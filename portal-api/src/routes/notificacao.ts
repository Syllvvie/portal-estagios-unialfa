import { Router } from "express";
import { AppDataSource } from "../database/data-source";
import { Notificacao } from "../models/Notificacao";
import { NotificacaoRepository } from "../repositories/NotificacaoRepository";
import { NotificacaoController } from "../controllers/NotificacaoController";

const router = Router();

const notificacaoRepository = new NotificacaoRepository(AppDataSource.getRepository(Notificacao));
const notificacaoController = new NotificacaoController(notificacaoRepository);

router.get("/", notificacaoController.minhasNotificacoes);     // GET /notificacoes
router.patch("/:id/lida", notificacaoController.marcarLida);   // PATCH /notificacoes/:id/lida

export default router;
