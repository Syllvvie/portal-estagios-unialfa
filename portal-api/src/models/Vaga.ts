import {
  Column,
  CreateDateColumn,
  Entity,
  ManyToOne,
  JoinColumn,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from "typeorm";
import { Empresa } from "./Empresa";
import { Candidatura } from "./Candidatura";

@Entity({ name: "vagas" })
export class Vaga {
  @PrimaryGeneratedColumn()
  id!: number;

  @Column({ type: "varchar", length: 150 })
  titulo!: string;

  @Column({ type: "text" })
  descricao!: string;

  @Column({ type: "varchar", length: 100, nullable: true })
  area!: string | null;

  @Column({ type: "varchar", length: 150, nullable: true })
  requisitos!: string | null;

  @Column({ type: "decimal", precision: 8, scale: 2, nullable: true })
  bolsa!: number | null;

  @Column({ type: "varchar", length: 150, nullable: true })
  local!: string | null;

  @Column({ type: "int", nullable: true })
  carga_horaria!: number | null; // horas semanais

  @Column({ type: "date", nullable: true })
  data_encerramento!: Date | null;

  @Column({ type: "boolean", default: true })
  ativa!: boolean;

  @ManyToOne(() => Empresa, (e) => e.vagas)
  @JoinColumn({ name: "empresa_id" })
  empresa!: Empresa;

  @Column({ name: "empresa_id" })
  empresa_id!: number;

  @OneToMany(() => Candidatura, (c) => c.vaga)
  candidaturas!: Candidatura[];

  @CreateDateColumn({ name: "created_at" })
  created_at!: Date;

  @UpdateDateColumn({ name: "updated_at" })
  updated_at!: Date;
}
