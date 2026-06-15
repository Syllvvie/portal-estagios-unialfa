package com.unialfa.gui;

import com.unialfa.model.Candidatura;
import com.unialfa.service.CandidaturaService;

import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;

public class CandidaturaGui extends JFrame implements PainelDefault {

    private final CandidaturaService service;
    private final JTable table = new JTable();

    public CandidaturaGui(CandidaturaService service) {
        this.service = service;
        setTitle("Consulta de Candidaturas");
        setSize(1050, 480);
        setLocationRelativeTo(null);

        var btnAtualizar = new JButton("Atualizar");
        btnAtualizar.addActionListener(e -> atualizarTabela());

        var topPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 8));
        topPanel.setBorder(BorderFactory.createTitledBorder("Candidaturas Realizadas"));
        topPanel.add(btnAtualizar);

        table.setDefaultEditor(Object.class, null);
        table.setRowHeight(22);
        table.setFont(fontePadrao());
        table.getTableHeader().setFont(fonteTitulo());

        getContentPane().add(topPanel, BorderLayout.NORTH);
        getContentPane().add(new JScrollPane(table), BorderLayout.CENTER);
        atualizarTabela();
    }

    private void atualizarTabela() {
        var model = new DefaultTableModel(
            new String[]{"ID", "Aluno", "RA", "Vaga", "Empresa", "Status", "Data"}, 0);
        for (Candidatura c : service.listar()) {
            model.addRow(new Object[]{
                c.getId(),
                c.getAlunoNome()  != null ? c.getAlunoNome()  : "-",
                c.getAlunoRa()    != null ? c.getAlunoRa()    : "-",
                c.getVagaTitulo() != null ? c.getVagaTitulo() : "-",
                c.getEmpresaNome()!= null ? c.getEmpresaNome(): "-",
                c.getStatusLabel(),
                c.getCreatedAt()  != null ? c.getCreatedAt().substring(0, 10) : "-"
            });
        }
        table.setModel(model);
    }
}
