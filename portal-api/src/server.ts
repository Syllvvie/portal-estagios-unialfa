import "reflect-metadata";
import "dotenv/config";

import express, { NextFunction, Request, Response } from "express";
import cors from "cors";
import { ZodError } from "zod";

import { AppDataSource } from "./database/data-source";
import routes from "./routes";
import AppError from "./utils/AppError";

const app = express();
const PORT = process.env.PORT ?? 3000;

app.use(cors());
app.use(express.json());
app.use(routes);

// Middleware de tratamento de erros
const handleErrorMiddleware = (
  error: Error,
  _req: Request,
  res: Response,
  _next: NextFunction
) => {
  if (error instanceof ZodError) {
    return res.status(400).json({
      message: "Erro de validação",
      issues: error.format(),
    });
  }

  if (error instanceof AppError) {
    return res.status(error.statusCode).json({
      status: "erro",
      message: error.message,
    });
  }

  console.error(error);
  return res.status(500).json({
    status: "erro",
    message: "Erro interno do servidor",
  });
};

app.use(handleErrorMiddleware);

AppDataSource.initialize()
  .then(() => {
    console.info("Banco de dados conectado com sucesso");
    app.listen(Number(PORT), () => {
      console.log(`API rodando na porta ${PORT}`);
    });
  })
  .catch((err) => {
    console.error("Falha ao conectar ao banco de dados:", err);
    process.exit(1);
  });
