package com.unialfa.service;

import com.unialfa.dao.CandidaturaDao;
import com.unialfa.model.Candidatura;

import java.util.List;

public class CandidaturaService {
    private final CandidaturaDao dao = new CandidaturaDao();

    public List<Candidatura> listar() {
        return dao.listar();
    }
}
