import { Request, Response, NextFunction } from "express";
import { verify } from "jsonwebtoken";
import authConfig from "../config/auth";
import AppError from "../utils/AppError";

export interface TokenPayload {
  id: number;
  tipo: "aluno" | "empresa";
}

export function autenticacao(req: Request, res: Response, next: NextFunction) {
  const authHeader = req.headers.authorization;

  if (!authHeader) {
    throw new AppError("Token inválido", 401);
  }

  const [, token] = authHeader.split(" ");

  try {
    const dados = verify(token, authConfig.jwt.secret) as TokenPayload;
    req.user = dados;
    return next();
  } catch {
    throw new AppError("Token inválido", 401);
  }
}

// Extensão do Request para carregar o usuário autenticado
declare global {
  namespace Express {
    interface Request {
      user?: TokenPayload;
    }
  }
}

export default autenticacao;
