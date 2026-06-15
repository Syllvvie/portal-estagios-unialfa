package com.unialfa.model;

public class Candidatura {
    private Long id;
    private Long alunoId;
    private String alunoNome;
    private String alunoRa;
    private Long vagaId;
    private String vagaTitulo;
    private String empresaNome;
    private String status;
    private String cartaApresentacao;
    private String observacao;
    private String createdAt;

    public Candidatura() {}

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public Long getAlunoId() { return alunoId; }
    public void setAlunoId(Long alunoId) { this.alunoId = alunoId; }

    public String getAlunoNome() { return alunoNome; }
    public void setAlunoNome(String alunoNome) { this.alunoNome = alunoNome; }

    public String getAlunoRa() { return alunoRa; }
    public void setAlunoRa(String alunoRa) { this.alunoRa = alunoRa; }

    public Long getVagaId() { return vagaId; }
    public void setVagaId(Long vagaId) { this.vagaId = vagaId; }

    public String getVagaTitulo() { return vagaTitulo; }
    public void setVagaTitulo(String vagaTitulo) { this.vagaTitulo = vagaTitulo; }

    public String getEmpresaNome() { return empresaNome; }
    public void setEmpresaNome(String empresaNome) { this.empresaNome = empresaNome; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getStatusLabel() {
        return switch (status == null ? "" : status) {
            case "em_analise" -> "Em Análise";
            case "aprovada"   -> "Aprovada";
            case "reprovada"  -> "Reprovada";
            case "cancelada"  -> "Cancelada";
            default           -> "Pendente";
        };
    }

    public String getCartaApresentacao() { return cartaApresentacao; }
    public void setCartaApresentacao(String cartaApresentacao) { this.cartaApresentacao = cartaApresentacao; }

    public String getObservacao() { return observacao; }
    public void setObservacao(String observacao) { this.observacao = observacao; }

    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }
}
