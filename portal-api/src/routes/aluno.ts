import { Router } from "express";
import { AppDataSource } from "../database/data-source";
import { Aluno } from "../models/Aluno";
import { AlunoRepository } from "../repositories/AlunoRepository";
import { AlunoService } from "../services/AlunoService";
import { AlunoController } from "../controllers/AlunoController";
import autenticacao from "../middlewares/autenticacao";

const router = Router();

const alunoRepository = new AlunoRepository(AppDataSource.getRepository(Aluno));
const alunoService = new AlunoService(alunoRepository);
const alunoController = new AlunoController(alunoService);

router.post("/", alunoController.registrar);
router.get("/", alunoController.listar);
router.get("/perfil", autenticacao, alunoController.perfil);
router.put("/trocar-senha", autenticacao, alunoController.trocarSenha); // PUT /alunos/trocar-senha
router.get("/:id", alunoController.buscarPorId);
router.put("/:id", autenticacao, alunoController.atualizar);
router.delete("/:id", autenticacao, alunoController.remover);

export default router;
