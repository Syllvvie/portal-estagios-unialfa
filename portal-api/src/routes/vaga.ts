import { Router } from "express";
import { AppDataSource } from "../database/data-source";
import { Empresa } from "../models/Empresa";
import { Vaga } from "../models/Vaga";
import { EmpresaRepository } from "../repositories/EmpresaRepository";
import { VagaRepository } from "../repositories/VagaRepository";
import { VagaService } from "../services/VagaService";
import { VagaController } from "../controllers/VagaController";
import autenticacao from "../middlewares/autenticacao";

const router = Router();

const empresaRepository = new EmpresaRepository(AppDataSource.getRepository(Empresa));
const vagaRepository = new VagaRepository(AppDataSource.getRepository(Vaga));
const vagaService = new VagaService(vagaRepository, empresaRepository);
const vagaController = new VagaController(vagaService);

router.get("/", vagaController.listar);                              // público
router.get("/minhas", autenticacao, vagaController.minhasVagas);     // empresa logada
router.get("/empresa/:empresa_id", vagaController.listarPorEmpresa); // público
router.get("/:id", vagaController.buscarPorId);                      // público
router.post("/", autenticacao, vagaController.criar);                // empresa
router.put("/:id", autenticacao, vagaController.atualizar);          // empresa
router.delete("/:id", autenticacao, vagaController.remover);         // empresa

export default router;
