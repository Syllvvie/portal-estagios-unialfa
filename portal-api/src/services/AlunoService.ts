import { hash } from "bcrypt";
import type { IAlunoRepository } from "../repositories/AlunoRepository";
import type { AlunoPublico } from "../models/Aluno";
import AppError from "../utils/AppError";

export class AlunoService {
  constructor(private readonly alunos: IAlunoRepository) {}

  async listar(): Promise<AlunoPublico[]> {
    return this.alunos.listarTodos();
  }

  async buscarPorId(id: number): Promise<AlunoPublico> {
    const a = await this.alunos.buscarPorId(id);
    if (!a) throw new AppError("Aluno não encontrado", 404);
    const { senha: _s, candidaturas: _c, ...pub } = a;
    return pub;
  }

  async registrar(input: {
    ra: string;
    nome: string;
    email: string;
    senha: string;
    telefone?: string;
    curso?: string;
    periodo?: number;
  }): Promise<AlunoPublico> {
    if (await this.alunos.buscarPorRa(input.ra)) {
      throw new AppError("RA já cadastrado", 409);
    }
    if (await this.alunos.buscarPorEmail(input.email)) {
      throw new AppError("E-mail já cadastrado", 409);
    }
    const senha = await hash(input.senha, 8);
    return this.alunos.criar({ ...input, senha, apto: true, ativo: true });
  }

  async atualizar(id: number, dados: Partial<{ nome: string; email: string; senha: string; telefone: string; curso: string; periodo: number }>): Promise<AlunoPublico> {
    const aluno = await this.alunos.buscarPorId(id);
    if (!aluno) throw new AppError("Aluno não encontrado", 404);

    if (dados.email && dados.email !== aluno.email) {
      if (await this.alunos.buscarPorEmail(dados.email)) {
        throw new AppError("E-mail já cadastrado", 409);
      }
    }

    if (dados.nome !== undefined) aluno.nome = dados.nome;
    if (dados.email !== undefined) aluno.email = dados.email;
    if (dados.telefone !== undefined) aluno.telefone = dados.telefone;
    if (dados.curso !== undefined) aluno.curso = dados.curso;
    if (dados.periodo !== undefined) aluno.periodo = dados.periodo;
    if (dados.senha) aluno.senha = await hash(dados.senha, 8);

    return this.alunos.salvar(aluno);
  }

  async remover(id: number): Promise<void> {
    const ok = await this.alunos.remover(id);
    if (!ok) throw new AppError("Aluno não encontrado", 404);
  }

  async trocarSenha(id: number, novaSenha: string): Promise<AlunoPublico> {
    const aluno = await this.alunos.buscarPorId(id);
    if (!aluno) throw new AppError("Aluno não encontrado", 404);

    aluno.senha = await hash(novaSenha, 8);
    aluno.primeiro_acesso = false;
    return this.alunos.salvar(aluno);
  }
}
