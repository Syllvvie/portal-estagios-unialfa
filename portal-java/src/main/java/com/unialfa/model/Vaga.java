package com.unialfa.model;

public class Vaga {
    private Long id;
    private String titulo;
    private String descricao;
    private String area;
    private String requisitos;
    private Double bolsa;
    private String local;
    private Integer cargaHoraria;
    private boolean ativa;
    private Long empresaId;
    private String empresaNome;

    public Vaga() {}

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getTitulo() { return titulo; }
    public void setTitulo(String titulo) { this.titulo = titulo; }

    public String getDescricao() { return descricao; }
    public void setDescricao(String descricao) { this.descricao = descricao; }

    public String getArea() { return area; }
    public void setArea(String area) { this.area = area; }

    public String getRequisitos() { return requisitos; }
    public void setRequisitos(String requisitos) { this.requisitos = requisitos; }

    public Double getBolsa() { return bolsa; }
    public void setBolsa(Double bolsa) { this.bolsa = bolsa; }

    public String getLocal() { return local; }
    public void setLocal(String local) { this.local = local; }

    public Integer getCargaHoraria() { return cargaHoraria; }
    public void setCargaHoraria(Integer cargaHoraria) { this.cargaHoraria = cargaHoraria; }

    public boolean isAtiva() { return ativa; }
    public void setAtiva(boolean ativa) { this.ativa = ativa; }

    public Long getEmpresaId() { return empresaId; }
    public void setEmpresaId(Long empresaId) { this.empresaId = empresaId; }

    public String getEmpresaNome() { return empresaNome; }
    public void setEmpresaNome(String empresaNome) { this.empresaNome = empresaNome; }

    @Override
    public String toString() { return titulo + " - " + (empresaNome != null ? empresaNome : ""); }
}
