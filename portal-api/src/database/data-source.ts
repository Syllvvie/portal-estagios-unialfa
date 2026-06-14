import "reflect-metadata";
import "dotenv/config";
import path from "path";
import { DataSource } from "typeorm";

import { Aluno } from "../models/Aluno";
import { Empresa } from "../models/Empresa";
import { Vaga } from "../models/Vaga";
import { Candidatura } from "../models/Candidatura";
import { Notificacao } from "../models/Notificacao";

const migrationsPath = path.join(__dirname, "/migrations");

export const AppDataSource = new DataSource({
  type: "mysql",
  host: process.env.DB_HOST ?? "localhost",
  port: Number(process.env.DB_PORT ?? 3306),
  username: process.env.DB_USER ?? "root",
  password: process.env.DB_PASS ?? "",
  database: process.env.DB_NAME ?? "portal_estagios",
  entities: [Aluno, Empresa, Vaga, Candidatura, Notificacao],
  migrations: [path.join(migrationsPath, "*.{js,ts}")],
  synchronize: false,
  logging: false,
});
