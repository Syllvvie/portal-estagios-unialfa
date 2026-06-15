package com.unialfa.model;

public class Aluno {
    private Long id;
    private String ra;
    private String nome;
    private String email;
    private String telefone;
    private String curso;
    private Integer periodo;
    private boolean apto;
    private boolean ativo;

    public Aluno() {}

    public Aluno(String ra, String nome, String email, String curso, Integer periodo) {
        this.ra = ra;
        this.nome = nome;
        this.email = email;
        this.curso = curso;
        this.periodo = periodo;
        this.apto = true;
        this.ativo = true;
    }

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getRa() { return ra; }
    public void setRa(String ra) { this.ra = ra; }

    public String getNome() { return nome; }
    public void setNome(String nome) { this.nome = nome; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getTelefone() { return telefone; }
    public void setTelefone(String telefone) { this.telefone = telefone; }

    public String getCurso() { return curso; }
    public void setCurso(String curso) { this.curso = curso; }

    public Integer getPeriodo() { return periodo; }
    public void setPeriodo(Integer periodo) { this.periodo = periodo; }

    public boolean isApto() { return apto; }
    public void setApto(boolean apto) { this.apto = apto; }

    public boolean isAtivo() { return ativo; }
    public void setAtivo(boolean ativo) { this.ativo = ativo; }

    @Override
    public String toString() { return nome + " (RA: " + ra + ")"; }
}
