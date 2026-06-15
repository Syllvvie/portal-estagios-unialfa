package com.unialfa.dao;

import com.unialfa.model.Aluno;
import org.mindrot.jbcrypt.BCrypt;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class AlunoDao extends Dao {

    private static final String SENHA_PADRAO = "UniAlfa@2026";

    private static String hashSenha(String senha) {
        return BCrypt.hashpw(senha, BCrypt.gensalt(8));
    }

    public List<Aluno> listar() {
        List<Aluno> lista = new ArrayList<>();
        try {
            var rs = getConnection()
                    .prepareStatement("SELECT * FROM alunos ORDER BY id")
                    .executeQuery();
            while (rs.next()) lista.add(mapear(rs));
        } catch (SQLException e) {
            System.err.println("Erro ao listar alunos: " + e.getMessage());
        }
        return lista;
    }

    public Aluno buscarPorId(Long id) {
        try {
            var ps = getConnection().prepareStatement("SELECT * FROM alunos WHERE id = ?");
            ps.setLong(1, id);
            var rs = ps.executeQuery();
            if (rs.next()) return mapear(rs);
        } catch (SQLException e) {
            System.err.println("Erro ao buscar aluno: " + e.getMessage());
        }
        return null;
    }

    public void inserir(Aluno a) throws SQLException {
        var sql = "INSERT INTO alunos (ra, nome, email, senha, telefone, curso, periodo, apto, ativo, primeiro_acesso) VALUES (?,?,?,?,?,?,?,?,?,?)";
        var ps = getConnection().prepareStatement(sql);
        ps.setString(1, a.getRa());
        ps.setString(2, a.getNome());
        ps.setString(3, a.getEmail());
        ps.setString(4, hashSenha(SENHA_PADRAO));
        ps.setString(5, a.getTelefone());
        ps.setString(6, a.getCurso());
        ps.setObject(7, a.getPeriodo());
        ps.setBoolean(8, a.isApto());
        ps.setBoolean(9, a.isAtivo());
        ps.setBoolean(10, true);
        ps.execute();
    }

    public void atualizar(Aluno a) throws SQLException {
        var sql = "UPDATE alunos SET ra=?, nome=?, email=?, telefone=?, curso=?, periodo=?, apto=?, ativo=? WHERE id=?";
        var ps = getConnection().prepareStatement(sql);
        ps.setString(1, a.getRa());
        ps.setString(2, a.getNome());
        ps.setString(3, a.getEmail());
        ps.setString(4, a.getTelefone());
        ps.setString(5, a.getCurso());
        ps.setObject(6, a.getPeriodo());
        ps.setBoolean(7, a.isApto());
        ps.setBoolean(8, a.isAtivo());
        ps.setLong(9, a.getId());
        ps.execute();
    }

    public void deletar(Long id) throws SQLException {
        var ps = getConnection().prepareStatement("DELETE FROM alunos WHERE id = ?");
        ps.setLong(1, id);
        ps.execute();
    }

    /** Importa lista de alunos de arquivo .txt (ra;nome;email;curso;periodo por linha) */
    public int importarDeTxt(List<String[]> linhas) {
        int importados = 0;
        // Gera o hash uma unica vez para todo o lote (performance)
        String senhaHash = hashSenha(SENHA_PADRAO);
        var sql = "INSERT IGNORE INTO alunos (ra, nome, email, senha, curso, periodo, apto, ativo, primeiro_acesso) VALUES (?,?,?,?,?,?,1,1,1)";
        try {
            var ps = getConnection().prepareStatement(sql);
            for (String[] campos : linhas) {
                if (campos.length < 3) continue;
                String ra = campos[0].trim();
                if (ra.length() > 6) ra = ra.substring(0, 6);
                ps.setString(1, ra);
                ps.setString(2, campos[1].trim());
                ps.setString(3, campos[2].trim());
                ps.setString(4, senhaHash);
                ps.setString(5, campos.length > 3 ? campos[3].trim() : "");
                ps.setObject(6, campos.length > 4 ? Integer.parseInt(campos[4].trim()) : null);
                ps.addBatch();
                importados++;
            }
            ps.executeBatch();
        } catch (SQLException e) {
            System.err.println("Erro na importacao: " + e.getMessage());
        }
        return importados;
    }

    private Aluno mapear(ResultSet rs) throws SQLException {
        var a = new Aluno();
        a.setId(rs.getLong("id"));
        a.setRa(rs.getString("ra"));
        a.setNome(rs.getString("nome"));
        a.setEmail(rs.getString("email"));
        a.setTelefone(rs.getString("telefone"));
        a.setCurso(rs.getString("curso"));
        a.setPeriodo(rs.getObject("periodo") != null ? rs.getInt("periodo") : null);
        a.setApto(rs.getBoolean("apto"));
        a.setAtivo(rs.getBoolean("ativo"));
        return a;
    }
}

