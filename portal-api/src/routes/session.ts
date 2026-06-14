import { Router } from "express";
import { AppDataSource } from "../database/data-source";
import { Aluno } from "../models/Aluno";
import { Empresa } from "../models/Empresa";
import { AlunoRepository } from "../repositories/AlunoRepository";
import { EmpresaRepository } from "../repositories/EmpresaRepository";
import { SessionService } from "../services/SessionService";
import { SessionController } from "../controllers/SessionController";

const router = Router();

const alunoRepository = new AlunoRepository(AppDataSource.getRepository(Aluno));
const empresaRepository = new EmpresaRepository(AppDataSource.getRepository(Empresa));
const sessionService = new SessionService(alunoRepository, empresaRepository);
const sessionController = new SessionController(sessionService);

// POST /session/aluno  → login com RA + senha
router.post("/aluno", sessionController.loginAluno);
// POST /session/empresa → login com email + senha
router.post("/empresa", sessionController.loginEmpresa);

export default router;
