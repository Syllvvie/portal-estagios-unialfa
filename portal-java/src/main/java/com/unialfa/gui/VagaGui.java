package com.unialfa.gui;

import com.unialfa.model.Vaga;
import com.unialfa.service.VagaService;

import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;

public class VagaGui extends JFrame implements PainelDefault {

    private final VagaService service;
    private final JTable table = new JTable();

    public VagaGui(VagaService service) {
        this.service = service;
        setTitle("Consulta de Vagas");
        setSize(950, 480);
        setLocationRelativeTo(null);

        var btnAtualizar = new JButton("Atualizar");
        btnAtualizar.addActionListener(e -> atualizarTabela());

        var topPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 8));
        topPanel.setBorder(BorderFactory.createTitledBorder("Vagas Cadastradas"));
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
            new String[]{"ID", "Titulo", "Empresa", "Area", "Bolsa (R$)", "Carga (h)", "Ativa"}, 0);
        for (Vaga v : service.listar()) {
            model.addRow(new Object[]{
                v.getId(), v.getTitulo(),
                v.getEmpresaNome() != null ? v.getEmpresaNome() : "-",
                v.getArea()        != null ? v.getArea()        : "-",
                v.getBolsa()       != null ? String.format("%.2f", v.getBolsa()) : "-",
                v.getCargaHoraria()!= null ? v.getCargaHoraria() + "h" : "-",
                v.isAtiva() ? "Sim" : "Nao"
            });
        }
        table.setModel(model);
    }
}
