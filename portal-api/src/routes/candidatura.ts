import { Router } from "express";
import { AppDataSource } from "../database/data-source";
import { Aluno } from "../models/Aluno";
import { Vaga } from "../models/Vaga";
import { Empresa } from "../models/Empresa";
import { Candidatura } from "../models/Candidatura";
import { Notificacao } from "../models/Notificacao";
import { AlunoRepository } from "../repositories/AlunoRepository";
import { VagaRepository } from "../repositories/VagaRepository";
import { EmpresaRepository } from "../repositories/EmpresaRepository";
import { CandidaturaRepository } from "../repositories/CandidaturaRepository";
import { NotificacaoRepository } from "../repositories/NotificacaoRepository";
import { CandidaturaService } from "../services/CandidaturaService";
import { CandidaturaController } from "../controllers/CandidaturaController";

const router = Router();

const alunoRepository = new AlunoRepository(AppDataSource.getRepository(Aluno));
const vagaRepository = new VagaRepository(AppDataSource.getRepository(Vaga));
const candidaturaRepository = new CandidaturaRepository(AppDataSource.getRepository(Candidatura));
const notificacaoRepository = new NotificacaoRepository(AppDataSource.getRepository(Notificacao));

const candidaturaService = new CandidaturaService(
  candidaturaRepository,
  vagaRepository,
  alunoRepository,
  notificacaoRepository
);
const candidaturaController = new CandidaturaController(candidaturaService);

router.get("/", candidaturaController.listar);                           // todas (admin)
router.get("/minhas", candidaturaController.minhasCandidaturas);         // aluno logado
router.get("/vaga/:vaga_id", candidaturaController.candidatosDaVaga);   // empresa vê candidatos
router.post("/", candidaturaController.candidatar);                      // aluno se candidata
router.patch("/:id/status", candidaturaController.atualizarStatus);      // empresa atualiza status
router.patch("/:id/cancelar", candidaturaController.cancelar);           // aluno cancela

export default router;
