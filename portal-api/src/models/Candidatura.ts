import {
  Column,
  CreateDateColumn,
  Entity,
  ManyToOne,
  JoinColumn,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from "typeorm";
import { Aluno } from "./Aluno";
import { Vaga } from "./Vaga";

export type StatusCandidatura = "pendente" | "em_analise" | "aprovada" | "reprovada" | "cancelada";

@Entity({ name: "candidaturas" })
export class Candidatura {
  @PrimaryGeneratedColumn()
  id!: number;

  @ManyToOne(() => Aluno, (a) => a.candidaturas)
  @JoinColumn({ name: "aluno_id" })
  aluno!: Aluno;

  @Column({ name: "aluno_id" })
  aluno_id!: number;

  @ManyToOne(() => Vaga, (v) => v.candidaturas)
  @JoinColumn({ name: "vaga_id" })
  vaga!: Vaga;

  @Column({ name: "vaga_id" })
  vaga_id!: number;

  @Column({
    type: "enum",
    enum: ["pendente", "em_analise", "aprovada", "reprovada", "cancelada"],
    default: "pendente",
  })
  status!: StatusCandidatura;

  @Column({ type: "text", nullable: true })
  carta_apresentacao!: string | null;

  @Column({ type: "text", nullable: true })
  observacao!: string | null; // observação da empresa ao mudar status

  @CreateDateColumn({ name: "created_at" })
  created_at!: Date;

  @UpdateDateColumn({ name: "updated_at" })
  updated_at!: Date;
}
