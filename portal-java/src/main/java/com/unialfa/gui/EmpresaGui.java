package com.unialfa.gui;

import com.unialfa.model.Empresa;
import com.unialfa.service.EmpresaService;

import javax.swing.*;
import javax.swing.event.ListSelectionEvent;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.ActionEvent;

public class EmpresaGui extends JFrame implements PainelDefault {

    private final EmpresaService service;

    private final JTextField idField     = new JTextField(6);
    private final JTextField nomeField   = new JTextField(30);
    private final JTextField cnpjField   = new JTextField(20);
    private final JTextField emailField  = new JTextField(25);
    private final JTextField statusField = new JTextField(12);

    private final JButton btnAprovar   = new JButton("Aprovar");
    private final JButton btnBloquear  = new JButton("Bloquear");
    private final JButton btnAtualizar = new JButton("Atualizar");

    private final JTable table = new JTable();

    public EmpresaGui(EmpresaService service) {
        this.service = service;
        setTitle("Gestao de Empresas");
        setSize(920, 480);
        setLocationRelativeTo(null);
        construirFormulario();
        construirTabela();
        atualizarTabela();
    }

    private void construirFormulario() {
        var panel = new JPanel(new GridBagLayout());
        panel.setBorder(BorderFactory.createTitledBorder("Empresa Selecionada"));

        idField.setEnabled(false);
        statusField.setEnabled(false);

        painelAdd(panel, label("ID:"), 0, 0);      painelAdd(panel, idField, 1, 0);
        painelAdd(panel, label("Nome:"), 2, 0);    painelAdd(panel, nomeField, 3, 0);
        painelAdd(panel, label("CNPJ:"), 0, 1);    painelAdd(panel, cnpjField, 1, 1);
        painelAdd(panel, label("E-mail:"), 2, 1);  painelAdd(panel, emailField, 3, 1);
        painelAdd(panel, label("Status:"), 0, 2);  painelAdd(panel, statusField, 1, 2);

        var btns = new JPanel(new FlowLayout(FlowLayout.LEFT, 8, 4));
        btnAprovar.addActionListener(this::aprovar);
        btnBloquear.addActionListener(this::bloquear);
        btnAtualizar.addActionListener(e -> atualizarTabela());
        btns.add(btnAprovar);
        btns.add(btnBloquear);
        btns.add(btnAtualizar);

        var gc = new GridBagConstraints();
        gc.gridx = 0; gc.gridy = 3; gc.gridwidth = 4;
        gc.insets = new Insets(8, 8, 4, 8);
        panel.add(btns, gc);

        getContentPane().add(panel, BorderLayout.NORTH);
    }

    private void construirTabela() {
        table.setDefaultEditor(Object.class, null);
        table.setRowHeight(22);
        table.setFont(fontePadrao());
        table.getTableHeader().setFont(fonteTitulo());
        table.getSelectionModel().addListSelectionListener(this::selecionar);
        getContentPane().add(new JScrollPane(table), BorderLayout.CENTER);
    }

    private void atualizarTabela() {
        var model = new DefaultTableModel(
            new String[]{"ID", "Nome", "CNPJ", "E-mail", "Telefone", "Area", "Status"}, 0);
        for (Empresa e : service.listar()) {
            model.addRow(new Object[]{
                e.getId(), e.getNome(), e.getCnpj(), e.getEmail(),
                e.getTelefone(), e.getAreaAtuacao(), e.getStatusLabel()
            });
        }
        table.setModel(model);
    }

    private void selecionar(ListSelectionEvent e) {
        int row = table.getSelectedRow();
        if (row < 0) return;
        idField.setText(s(table.getValueAt(row, 0)));
        nomeField.setText(s(table.getValueAt(row, 1)));
        cnpjField.setText(s(table.getValueAt(row, 2)));
        emailField.setText(s(table.getValueAt(row, 3)));
        statusField.setText(s(table.getValueAt(row, 6)));
    }

    private void aprovar(ActionEvent e) {
        if (idField.getText().isBlank()) { aviso("Selecione uma empresa."); return; }
        try {
            service.aprovar(Long.valueOf(idField.getText()));
            atualizarTabela();
            statusField.setText("Aprovada");
            JOptionPane.showMessageDialog(this, "Empresa aprovada!", "Sucesso", JOptionPane.INFORMATION_MESSAGE);
        } catch (Exception ex) { erro(ex.getMessage()); }
    }

    private void bloquear(ActionEvent e) {
        if (idField.getText().isBlank()) { aviso("Selecione uma empresa."); return; }
        int r = JOptionPane.showConfirmDialog(this, "Bloquear esta empresa?", "Confirmar", JOptionPane.YES_NO_OPTION);
        if (r != JOptionPane.YES_OPTION) return;
        try {
            service.bloquear(Long.valueOf(idField.getText()));
            atualizarTabela();
            statusField.setText("Bloqueada");
        } catch (Exception ex) { erro(ex.getMessage()); }
    }

    private JLabel label(String t) { var l = new JLabel(t); l.setFont(fontePadrao()); return l; }
    private String s(Object o) { return o != null ? o.toString() : ""; }
    private void aviso(String m) { JOptionPane.showMessageDialog(this, m, "Atencao", JOptionPane.WARNING_MESSAGE); }
    private void erro(String m)  { JOptionPane.showMessageDialog(this, "Erro: " + m, "Erro", JOptionPane.ERROR_MESSAGE); }
}
