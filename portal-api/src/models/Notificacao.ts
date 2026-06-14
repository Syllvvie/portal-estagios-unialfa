import {
  Column,
  CreateDateColumn,
  Entity,
  PrimaryGeneratedColumn,
} from "typeorm";

@Entity({ name: "notificacoes" })
export class Notificacao {
  @PrimaryGeneratedColumn()
  id!: number;

  @Column({ type: "int" })
  destinatario_id!: number; // id do aluno ou empresa

  @Column({ type: "enum", enum: ["aluno", "empresa"] })
  tipo_destinatario!: "aluno" | "empresa";

  @Column({ type: "varchar", length: 200 })
  titulo!: string;

  @Column({ type: "text" })
  mensagem!: string;

  @Column({ type: "boolean", default: false })
  lida!: boolean;

  @CreateDateColumn({ name: "created_at" })
  created_at!: Date;
}
