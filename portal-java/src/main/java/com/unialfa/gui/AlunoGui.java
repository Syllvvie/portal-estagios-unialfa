package com.unialfa.gui;

import com.unialfa.model.Aluno;
import com.unialfa.service.AlunoService;

import javax.swing.*;
import javax.swing.event.ListSelectionEvent;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.ActionEvent;

public class AlunoGui extends JFrame implements PainelDefault {

    private final AlunoService service;

    private final JTextField idField       = new JTextField(6);
    private final JTextField raField       = new JTextField(6);
    private final JTextField nomeField     = new JTextField(20);
    private final JTextField emailField    = new JTextField(20);
    private final JTextField telefoneField = new JTextField(15);
    private final JTextField cursoField    = new JTextField(20);
    private final JTextField periodoField  = new JTextField(4);
    private final JCheckBox  aptoCheck     = new JCheckBox("Apto para estagio");
    private final JCheckBox  ativoCheck    = new JCheckBox("Ativo");

    private final JButton btnNovo     = new JButton("Novo");
    private final JButton btnSalvar   = new JButton("Salvar");
    private final JButton btnDeletar  = new JButton("Deletar");
    private final JButton btnImportar = new JButton("Importar .txt");

    private final JTable table = new JTable();

    public AlunoGui(AlunoService service) {
        this.service = service;
        setTitle("Gestao de Alunos");
        setSize(1000, 540);
        setLocationRelativeTo(null);
        construirFormulario();
        construirTabela();
        atualizarTabela();
    }

    private void construirFormulario() {
        var panel = new JPanel(new GridBagLayout());
        panel.setBorder(BorderFactory.createTitledBorder("Dados do Aluno"));

        idField.setEnabled(false);
        aptoCheck.setSelected(true);
        ativoCheck.setSelected(true);

        // Limite de 6 dígitos no campo RA
        raField.setDocument(new javax.swing.text.PlainDocument() {
            @Override
            public void insertString(int offs, String str, javax.swing.text.AttributeSet a)
                    throws javax.swing.text.BadLocationException {
                if (str == null) return;
                String novo = getText(0, getLength()) + str;
                if (novo.matches("\\d{0,6}")) super.insertString(offs, str, a);
            }
        });

        painelAdd(panel, label("ID:"), 0, 0);
        painelAdd(panel, idField, 1, 0);

        painelAdd(panel, label("RA: *"), 0, 1);
        painelAdd(panel, raField, 1, 1);
        painelAdd(panel, label("Nome: *"), 2, 1);
        painelAdd(panel, nomeField, 3, 1);

        painelAdd(panel, label("E-mail:"), 0, 2);
        painelAdd(panel, emailField, 1, 2);
        painelAdd(panel, label("Telefone:"), 2, 2);
        painelAdd(panel, telefoneField, 3, 2);

        painelAdd(panel, label("Curso:"), 0, 3);
        painelAdd(panel, cursoField, 1, 3);
        painelAdd(panel, label("Periodo:"), 2, 3);
        painelAdd(panel, periodoField, 3, 3);

        painelAdd(panel, aptoCheck, 1, 4);
        painelAdd(panel, ativoCheck, 2, 4);

        var botoesPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 8, 4));
        btnNovo.addActionListener(this::novo);
        btnSalvar.addActionListener(this::salvar);
        btnDeletar.addActionListener(this::deletar);
        btnImportar.addActionListener(this::importarTxt);
        botoesPanel.add(btnNovo);
        botoesPanel.add(btnSalvar);
        botoesPanel.add(btnDeletar);
        botoesPanel.add(btnImportar);

        var gcBtn = new GridBagConstraints();
        gcBtn.gridx = 0; gcBtn.gridy = 5; gcBtn.gridwidth = 4;
        gcBtn.insets = new Insets(8, 8, 4, 8);
        panel.add(botoesPanel, gcBtn);

        getContentPane().add(panel, BorderLayout.NORTH);
    }

    private void construirTabela() {
        table.setDefaultEditor(Object.class, null);
        table.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        table.setFont(fontePadrao());
        table.getTableHeader().setFont(fonteTitulo());
        table.setRowHeight(22);
        table.getSelectionModel().addListSelectionListener(this::selecionarAluno);
        getContentPane().add(new JScrollPane(table), BorderLayout.CENTER);
    }

    private void atualizarTabela() {
        var model = new DefaultTableModel(
            new String[]{"ID", "RA", "Nome", "E-mail", "Telefone", "Curso", "Periodo", "Apto", "Ativo"}, 0);
        for (Aluno a : service.listar()) {
            model.addRow(new Object[]{
                a.getId(), a.getRa(), a.getNome(), a.getEmail(),
                a.getTelefone() != null ? a.getTelefone() : "-",
                a.getCurso()    != null ? a.getCurso()    : "-",
                a.getPeriodo()  != null ? a.getPeriodo() + "o" : "-",
                a.isApto()  ? "Sim" : "Nao",
                a.isAtivo() ? "Ativo" : "Inativo"
            });
        }
        table.setModel(model);
    }

    private void selecionarAluno(ListSelectionEvent e) {
        int row = table.getSelectedRow();
        if (row < 0) return;
        idField.setText(s(table.getValueAt(row, 0)));
        raField.setText(s(table.getValueAt(row, 1)));
        nomeField.setText(s(table.getValueAt(row, 2)));
        emailField.setText(s(table.getValueAt(row, 3)));
        telefoneField.setText(s(table.getValueAt(row, 4)).equals("-") ? "" : s(table.getValueAt(row, 4)));
        cursoField.setText(s(table.getValueAt(row, 5)).equals("-") ? "" : s(table.getValueAt(row, 5)));
        var per = s(table.getValueAt(row, 6)).replace("o", "");
        periodoField.setText(per.equals("-") ? "" : per);
        aptoCheck.setSelected("Sim".equals(table.getValueAt(row, 7)));
        ativoCheck.setSelected("Ativo".equals(table.getValueAt(row, 8)));
    }

    private void novo(ActionEvent e) { limpar(); atualizarTabela(); }

    private void salvar(ActionEvent e) {
        if (raField.getText().isBlank() || nomeField.getText().isBlank()) {
            JOptionPane.showMessageDialog(this, "RA e Nome sao obrigatorios.", "Atencao", JOptionPane.WARNING_MESSAGE);
            return;
        }
        if (raField.getText().length() > 6) {
            JOptionPane.showMessageDialog(this, "RA deve ter no maximo 6 digitos.", "Atencao", JOptionPane.WARNING_MESSAGE);
            return;
        }
        try {
            var a = new Aluno();
            if (!idField.getText().isBlank()) a.setId(Long.valueOf(idField.getText()));
            a.setRa(raField.getText().trim());
            a.setNome(nomeField.getText().trim());
            a.setEmail(emailField.getText().trim());
            if (!telefoneField.getText().isBlank()) a.setTelefone(telefoneField.getText().trim());
            a.setCurso(cursoField.getText().trim());
            if (!periodoField.getText().isBlank()) a.setPeriodo(Integer.valueOf(periodoField.getText().trim()));
            a.setApto(aptoCheck.isSelected());
            a.setAtivo(ativoCheck.isSelected());
            service.salvar(a);
            limpar();
            atualizarTabela();
            JOptionPane.showMessageDialog(this, "Aluno salvo com sucesso!", "Sucesso", JOptionPane.INFORMATION_MESSAGE);
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Erro ao salvar: " + ex.getMessage(), "Erro", JOptionPane.ERROR_MESSAGE);
        }
    }

    private void deletar(ActionEvent e) {
        if (idField.getText().isBlank()) return;
        int r = JOptionPane.showConfirmDialog(this, "Deseja excluir este aluno?", "Confirmar", JOptionPane.YES_NO_OPTION);
        if (r != JOptionPane.YES_OPTION) return;
        try {
            service.deletar(Long.valueOf(idField.getText()));
            limpar();
            atualizarTabela();
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Erro ao deletar: " + ex.getMessage(), "Erro", JOptionPane.ERROR_MESSAGE);
        }
    }

    private void importarTxt(ActionEvent e) {
        var fc = new JFileChooser();
        fc.setFileFilter(new javax.swing.filechooser.FileNameExtensionFilter("Arquivo de texto (*.txt)", "txt"));
        if (fc.showOpenDialog(this) != JFileChooser.APPROVE_OPTION) return;
        String resultado = service.importarDeTxt(fc.getSelectedFile().getAbsolutePath());
        JOptionPane.showMessageDialog(this, resultado, "Importacao", JOptionPane.INFORMATION_MESSAGE);
        atualizarTabela();
    }

    private void limpar() {
        idField.setText(""); raField.setText(""); nomeField.setText("");
        emailField.setText(""); telefoneField.setText(""); cursoField.setText("");
        periodoField.setText("");
        aptoCheck.setSelected(true); ativoCheck.setSelected(true);
    }

    private JLabel label(String t) { var l = new JLabel(t); l.setFont(fontePadrao()); return l; }
    private String s(Object o) { return o != null ? o.toString() : ""; }
}