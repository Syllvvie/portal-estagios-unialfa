package com.unialfa.service;

import com.unialfa.dao.EmpresaDao;
import com.unialfa.model.Empresa;

import java.sql.SQLException;
import java.util.List;

public class EmpresaService {
    private final EmpresaDao dao = new EmpresaDao();

    public List<Empresa> listar() {
        return dao.listar();
    }

    public Empresa buscarPorId(Long id) {
        return dao.buscarPorId(id);
    }

    public void aprovar(Long id) throws SQLException {
        dao.atualizarStatus(id, "aprovada");
    }

    public void bloquear(Long id) throws SQLException {
        dao.atualizarStatus(id, "bloqueada");
    }

    public void reativar(Long id) throws SQLException {
        dao.atualizarStatus(id, "pendente");
    }
}
