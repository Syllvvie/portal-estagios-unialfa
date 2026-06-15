package com.unialfa.service;

import com.unialfa.dao.AlunoDao;
import com.unialfa.model.Aluno;

import java.io.BufferedReader;
import java.io.FileReader;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class AlunoService {
    private final AlunoDao dao = new AlunoDao();

    public List<Aluno> listar() {
        return dao.listar();
    }

    public Aluno buscarPorId(Long id) {
        return dao.buscarPorId(id);
    }

    public void salvar(Aluno a) throws SQLException {
        if (a.getId() == null) {
            dao.inserir(a);
        } else {
            dao.atualizar(a);
        }
    }

    public void deletar(Long id) throws SQLException {
        dao.deletar(id);
    }

    /** Importa alunos de um arquivo .txt com campos separados por ponto-e-vírgula:
     *  ra;nome;email;curso;periodo */
    public String importarDeTxt(String caminho) {
        List<String[]> linhas = new ArrayList<>();
        try (var br = new BufferedReader(new FileReader(caminho))) {
            String linha;
            while ((linha = br.readLine()) != null) {
                if (linha.isBlank()) continue;
                linhas.add(linha.split(";"));
            }
        } catch (Exception e) {
            return "Erro ao ler arquivo: " + e.getMessage();
        }
        int n = dao.importarDeTxt(linhas);
        return n + " aluno(s) importado(s) com sucesso.";
    }
}
