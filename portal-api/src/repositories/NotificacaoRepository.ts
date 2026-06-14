import type { Repository } from "typeorm";
import type { Notificacao } from "../models/Notificacao";

export class NotificacaoRepository {
  constructor(private readonly repo: Repository<Notificacao>) {}

  async criar(dados: Partial<Notificacao>): Promise<Notificacao> {
    const ent = this.repo.create(dados);
    return this.repo.save(ent);
  }

  async listarPorDestinatario(destinatario_id: number, tipo: "aluno" | "empresa"): Promise<Notificacao[]> {
    return this.repo.find({
      where: { destinatario_id, tipo_destinatario: tipo },
      order: { id: "DESC" },
    });
  }

  async marcarLida(id: number): Promise<boolean> {
    const r = await this.repo.update(id, { lida: true });
    return (r.affected ?? 0) > 0;
  }
}
