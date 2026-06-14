import { Router } from "express";
import { AppDataSource } from "../database/data-source";
import { Empresa } from "../models/Empresa";
import { EmpresaRepository } from "../repositories/EmpresaRepository";
import { EmpresaService } from "../services/EmpresaService";
import { EmpresaController } from "../controllers/EmpresaController";
import autenticacao from "../middlewares/autenticacao";

const router = Router();

const empresaRepository = new EmpresaRepository(AppDataSource.getRepository(Empresa));
const empresaService = new EmpresaService(empresaRepository);
const empresaController = new EmpresaController(empresaService);

router.post("/", empresaController.registrar);           // POST /empresas → cadastro
router.get("/", empresaController.listar);               // GET  /empresas
router.get("/perfil", autenticacao, empresaController.perfil); // GET /empresas/perfil
router.get("/:id", empresaController.buscarPorId);       // GET  /empresas/:id
router.put("/:id", autenticacao, empresaController.atualizar); // PUT  /empresas/:id
router.patch("/:id/status", autenticacao, empresaController.atualizarStatus); // PATCH /empresas/:id/status
router.delete("/:id", autenticacao, empresaController.remover); // DELETE /empresas/:id

export default router;
