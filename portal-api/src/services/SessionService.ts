import { compare } from "bcrypt";
import { sign } from "jsonwebtoken";
import authConfig from "../config/auth";
import AppError from "../utils/AppError";
import type { IAlunoRepository } from "../repositories/AlunoRepository";
import type { IEmpresaRepository } from "../repositories/EmpresaRepository";

export class SessionService {
  constructor(
    private readonly alunos: IAlunoRepository,
    private readonly empresas: IEmpresaRepository
  ) {}

  // Login do aluno: usa RA + senha
  async loginAluno(ra: string, senha: string) {
    const aluno = await this.alunos.buscarPorRa(ra);
    if (!aluno) throw new AppError("RA ou senha inválidos", 401);
    if (!aluno.ativo) throw new AppError("Aluno inativo", 403);

    const senhaOk = await compare(senha, aluno.senha);
    if (!senhaOk) throw new AppError("RA ou senha inválidos", 401);

    const token = sign(
      { id: aluno.id, tipo: "aluno" },
      authConfig.jwt.secret,
      { expiresIn: authConfig.jwt.expiresIn as any }
    );

    const { senha: _s, candidaturas: _c, ...pub } = aluno;
    return { token, aluno: pub, primeiro_acesso: aluno.primeiro_acesso };
  }

  // Login da empresa: usa email + senha
  async loginEmpresa(email: string, senha: string) {
    const empresa = await this.empresas.buscarPorEmail(email);
    if (!empresa) throw new AppError("E-mail ou senha inválidos", 401);
    if (empresa.status === "bloqueada") throw new AppError("Empresa bloqueada", 403);

    const senhaOk = await compare(senha, empresa.senha);
    if (!senhaOk) throw new AppError("E-mail ou senha inválidos", 401);

    const token = sign(
      { id: empresa.id, tipo: "empresa" },
      authConfig.jwt.secret,
      { expiresIn: authConfig.jwt.expiresIn as any }
    );

    const { senha: _s, vagas: _v, ...pub } = empresa;
    return { token, empresa: pub };
  }
}
