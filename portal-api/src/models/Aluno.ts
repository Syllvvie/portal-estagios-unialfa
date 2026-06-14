import {
  Column,
  CreateDateColumn,
  Entity,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from "typeorm";
import { Candidatura } from "./Candidatura";

@Entity({ name: "alunos" })
export class Aluno {
  @PrimaryGeneratedColumn()
  id!: number;

  @Column({ type: "varchar", length: 6, unique: true })
  ra!: string; // Registro Academico - login do aluno (max 6 digitos)

  @Column({ type: "varchar", length: 150 })
  nome!: string;

  @Column({ type: "varchar", length: 150, unique: true })
  email!: string;

  @Column({ type: "varchar", length: 255 })
  senha!: string;

  @Column({ type: "varchar", length: 20, nullable: true })
  telefone!: string | null;

  @Column({ type: "varchar", length: 150, nullable: true })
  curso!: string | null;

  @Column({ type: "int", nullable: true })
  periodo!: number | null;

  @Column({ type: "boolean", default: true })
  apto!: boolean; // controlado pelo back-office Java

  @Column({ type: "boolean", default: true })
  ativo!: boolean;

  @Column({ name: "primeiro_acesso", type: "boolean", default: true })
  primeiro_acesso!: boolean; // true = deve trocar senha no primeiro login

  @OneToMany(() => Candidatura, (c) => c.aluno)
  candidaturas!: Candidatura[];

  @CreateDateColumn({ name: "created_at" })
  created_at!: Date;

  @UpdateDateColumn({ name: "updated_at" })
  updated_at!: Date;
}

export type AlunoPublico = Omit<Aluno, "senha" | "candidaturas">;
