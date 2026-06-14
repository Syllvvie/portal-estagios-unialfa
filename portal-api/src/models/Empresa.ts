import {
  Column,
  CreateDateColumn,
  Entity,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from "typeorm";
import { Vaga } from "./Vaga";

export type StatusEmpresa = "pendente" | "aprovada" | "bloqueada";

@Entity({ name: "empresas" })
export class Empresa {
  @PrimaryGeneratedColumn()
  id!: number;

  @Column({ type: "varchar", length: 150 })
  nome!: string;

  @Column({ type: "varchar", length: 18, unique: true })
  cnpj!: string;

  @Column({ type: "varchar", length: 150, unique: true })
  email!: string;

  @Column({ type: "varchar", length: 255 })
  senha!: string;

  @Column({ type: "varchar", length: 20, nullable: true })
  telefone!: string | null;

  @Column({ type: "varchar", length: 200, nullable: true })
  endereco!: string | null;

  @Column({ type: "varchar", length: 100, nullable: true })
  area_atuacao!: string | null;

  @Column({
    type: "enum",
    enum: ["pendente", "aprovada", "bloqueada"],
    default: "pendente",
  })
  status!: StatusEmpresa;

  @OneToMany(() => Vaga, (v) => v.empresa)
  vagas!: Vaga[];

  @CreateDateColumn({ name: "created_at" })
  created_at!: Date;

  @UpdateDateColumn({ name: "updated_at" })
  updated_at!: Date;
}

export type EmpresaPublica = Omit<Empresa, "senha" | "vagas">;
