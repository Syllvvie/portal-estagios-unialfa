package com.unialfa.dao;

import com.unialfa.model.Candidatura;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class CandidaturaDao extends Dao {

    public List<Candidatura> listar() {
        List<Candidatura> lista = new ArrayList<>();
        try {
            var sql = """
                SELECT c.*, a.nome AS aluno_nome, a.ra AS aluno_ra,
                       v.titulo AS vaga_titulo, e.nome AS empresa_nome
                FROM candidaturas c
                LEFT JOIN alunos a ON a.id = c.aluno_id
                LEFT JOIN vagas v ON v.id = c.vaga_id
                LEFT JOIN empresas e ON e.id = v.empresa_id
                ORDER BY c.id DESC
                """;
            var rs = getConnection().prepareStatement(sql).executeQuery();
            while (rs.next()) lista.add(mapear(rs));
        } catch (SQLException e) {
            System.err.println("Erro ao listar candidaturas: " + e.getMessage());
        }
        return lista;
    }

    private Candidatura mapear(ResultSet rs) throws SQLException {
        var c = new Candidatura();
        c.setId(rs.getLong("id"));
        c.setAlunoId(rs.getLong("aluno_id"));
        c.setAlunoNome(rs.getString("aluno_nome"));
        c.setAlunoRa(rs.getString("aluno_ra"));
        c.setVagaId(rs.getLong("vaga_id"));
        c.setVagaTitulo(rs.getString("vaga_titulo"));
        c.setEmpresaNome(rs.getString("empresa_nome"));
        c.setStatus(rs.getString("status"));
        c.setCartaApresentacao(rs.getString("carta_apresentacao"));
        c.setObservacao(rs.getString("observacao"));
        c.setCreatedAt(rs.getString("created_at"));
        return c;
    }
}
