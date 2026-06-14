import { Router } from "express";

import autenticacao from "../middlewares/autenticacao";
import alunoRoutes from "./aluno";
import empresaRoutes from "./empresa";
import vagaRoutes from "./vaga";
import candidaturaRoutes from "./candidatura";
import sessionRoutes from "./session";
import notificacaoRoutes from "./notificacao";

const routes = Router();

// Rotas públicas
routes.use("/session", sessionRoutes);
routes.use("/alunos", alunoRoutes);
routes.use("/empresas", empresaRoutes);
routes.use("/vagas", vagaRoutes); // listar vagas é público

// Rotas protegidas (requerem token)
routes.use(autenticacao);
routes.use("/candidaturas", candidaturaRoutes);
routes.use("/notificacoes", notificacaoRoutes);

export default routes;
