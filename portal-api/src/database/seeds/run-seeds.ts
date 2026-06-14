import "reflect-metadata";
import "dotenv/config";
import { hash } from "bcrypt";
import { AppDataSource } from "../data-source";
import { Aluno } from "../../models/Aluno";
import { Empresa } from "../../models/Empresa";
import { Vaga } from "../../models/Vaga";

const BCRYPT_ROUNDS = 8;

async function rodarSeed() {
  await AppDataSource.initialize();

  const alunoRepo = AppDataSource.getRepository(Aluno);
  const empresaRepo = AppDataSource.getRepository(Empresa);
  const vagaRepo = AppDataSource.getRepository(Vaga);

  // Seed de alunos
  const alunos = [
    { ra: "230001", nome: "Joao Silva", email: "joao@aluno.unialfa.com", curso: "Tecnologia em Sistemas para Internet", periodo: 3 },
    { ra: "230002", nome: "Maria Souza", email: "maria@aluno.unialfa.com", curso: "Tecnologia em Sistemas para Internet", periodo: 2 },
  ];

  for (const a of alunos) {
    const existe = await alunoRepo.exists({ where: { ra: a.ra } });
    if (!existe) {
      const senha = await hash("UniAlfa@2026", BCRYPT_ROUNDS);
      await alunoRepo.save(alunoRepo.create({ ...a, senha, apto: true, ativo: true, primeiro_acesso: true }));
      console.log(`Aluno criado: ${a.nome} (RA: ${a.ra})`);
    }
  }

  // Seed de empresa
  const emailEmpresa = "techsolutions@empresa.com";
  const empresaExiste = await empresaRepo.exists({ where: { email: emailEmpresa } });
  let empresa: Empresa;
  if (!empresaExiste) {
    const senha = await hash("empresa123", BCRYPT_ROUNDS);
    empresa = await empresaRepo.save(empresaRepo.create({
      nome: "Tech Solutions LTDA",
      cnpj: "12.345.678/0001-90",
      email: emailEmpresa,
      senha,
      telefone: "(44) 9999-0000",
      endereco: "Av. Brasil, 100 - Umuarama/PR",
      area_atuacao: "Tecnologia da Informação",
      status: "aprovada",
    }));
    console.log(`Empresa criada: ${empresa.nome}`);
  } else {
    empresa = await empresaRepo.findOne({ where: { email: emailEmpresa } }) as Empresa;
  }

  // Seed de vagas
  const vagasExistem = await vagaRepo.exists({ where: { empresa_id: empresa.id } });
  if (!vagasExistem) {
    await vagaRepo.save(vagaRepo.create({
      titulo: "Estágio em Desenvolvimento Web",
      descricao: "Desenvolvimento de sistemas web utilizando tecnologias modernas como React e Node.js. O estagiário participará de projetos reais da empresa.",
      area: "Desenvolvimento Web",
      requisitos: "HTML, CSS, JavaScript básico",
      bolsa: 800.00,
      local: "Umuarama - PR",
      carga_horaria: 20,
      empresa_id: empresa.id,
      ativa: true,
    }));
    await vagaRepo.save(vagaRepo.create({
      titulo: "Estágio em Banco de Dados",
      descricao: "Apoio na modelagem e manutenção de bancos de dados MySQL e PostgreSQL.",
      area: "Banco de Dados",
      requisitos: "SQL básico",
      bolsa: 700.00,
      local: "Umuarama - PR",
      carga_horaria: 20,
      empresa_id: empresa.id,
      ativa: true,
    }));
    console.log("Vagas criadas com sucesso");
  }

  await AppDataSource.destroy();
  console.log("\nSeed concluído com sucesso!");
  console.log("Aluno de teste: RA=230001 / senha=UniAlfa@2026 (primeiro acesso)");
  console.log("Empresa de teste: email=techsolutions@empresa.com / senha=empresa123");
}

rodarSeed().catch(async (err) => {
  console.error(err);
  if (AppDataSource.isInitialized) await AppDataSource.destroy();
  process.exit(1);
});
