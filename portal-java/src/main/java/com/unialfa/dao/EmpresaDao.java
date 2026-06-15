package com.unialfa.dao;

import com.unialfa.model.Empresa;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class EmpresaDao extends Dao {

    public List<Empresa> listar() {
        List<Empresa> lista = new ArrayList<>();
        try {
            var rs = getConnection()
                .prepareStatement("SELECT * FROM empresas ORDER BY id")
                .executeQuery();
            while (rs.next()) lista.add(mapear(rs));
        } catch (SQLException e) {
            System.err.println("Erro ao listar empresas: " + e.getMessage());
        }
        return lista;
    }

    public Empresa buscarPorId(Long id) {
        try {
            var ps = getConnection().prepareStatement("SELECT * FROM empresas WHERE id = ?");
            ps.setLong(1, id);
            var rs = ps.executeQuery();
            if (rs.next()) return mapear(rs);
        } catch (SQLException e) {
            System.err.println("Erro ao buscar empresa: " + e.getMessage());
        }
        return null;
    }

    public void atualizarStatus(Long id, String status) throws SQLException {
        var ps = getConnection().prepareStatement("UPDATE empresas SET status = ? WHERE id = ?");
        ps.setString(1, status);
        ps.setLong(2, id);
        ps.execute();
    }

    private Empresa mapear(ResultSet rs) throws SQLException {
        var e = new Empresa();
        e.setId(rs.getLong("id"));
        e.setNome(rs.getString("nome"));
        e.setCnpj(rs.getString("cnpj"));
        e.setEmail(rs.getString("email"));
        e.setTelefone(rs.getString("telefone"));
        e.setEndereco(rs.getString("endereco"));
        e.setAreaAtuacao(rs.getString("area_atuacao"));
        e.setStatus(rs.getString("status"));
        return e;
    }
}
