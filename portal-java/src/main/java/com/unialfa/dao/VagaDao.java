package com.unialfa.dao;

import com.unialfa.model.Vaga;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class VagaDao extends Dao {

    public List<Vaga> listar() {
        List<Vaga> lista = new ArrayList<>();
        try {
            var sql = "SELECT v.*, e.nome AS empresa_nome FROM vagas v LEFT JOIN empresas e ON e.id = v.empresa_id ORDER BY v.id DESC";
            var rs = getConnection().prepareStatement(sql).executeQuery();
            while (rs.next()) lista.add(mapear(rs));
        } catch (SQLException e) {
            System.err.println("Erro ao listar vagas: " + e.getMessage());
        }
        return lista;
    }

    private Vaga mapear(ResultSet rs) throws SQLException {
        var v = new Vaga();
        v.setId(rs.getLong("id"));
        v.setTitulo(rs.getString("titulo"));
        v.setDescricao(rs.getString("descricao"));
        v.setArea(rs.getString("area"));
        v.setRequisitos(rs.getString("requisitos"));
        v.setBolsa(rs.getObject("bolsa") != null ? rs.getDouble("bolsa") : null);
        v.setLocal(rs.getString("local"));
        v.setCargaHoraria(rs.getObject("carga_horaria") != null ? rs.getInt("carga_horaria") : null);
        v.setAtiva(rs.getBoolean("ativa"));
        v.setEmpresaId(rs.getLong("empresa_id"));
        v.setEmpresaNome(rs.getString("empresa_nome"));
        return v;
    }
}
