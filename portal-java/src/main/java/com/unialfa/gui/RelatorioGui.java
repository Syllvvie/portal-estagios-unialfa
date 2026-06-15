package com.unialfa.gui;

import com.unialfa.service.RelatorioService;

import javax.swing.*;
import javax.swing.filechooser.FileNameExtensionFilter;
import java.awt.*;

public class RelatorioGui extends JFrame implements PainelDefault {

    private final RelatorioService service;

    // Combo para escolher o formato
    private final JComboBox<String> comboFormato = new JComboBox<>(new String[]{"TXT", "CSV", "PDF"});

    public RelatorioGui(RelatorioService service) {
        this.service = service;
        setTitle("Geracao de Relatorios");
        setSize(400, 320);
        setLocationRelativeTo(null);
        construirLayout();
    }

    private void construirLayout() {
        var panel = new JPanel(new GridBagLayout());
        panel.setBorder(BorderFactory.createTitledBorder("Relatorios"));

        // Seletor de formato
        var labelFormato = new JLabel("Formato de saida:");
        labelFormato.setFont(fontePadrao());
        comboFormato.setFont(fontePadrao());

        var gcFormato = new GridBagConstraints();
        gcFormato.gridx = 0; gcFormato.gridy = 0; gcFormato.gridwidth = 2;
        gcFormato.insets = new Insets(6, 8, 10, 8);
        gcFormato.fill = GridBagConstraints.HORIZONTAL;

        var formatoPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 6, 0));
        formatoPanel.add(labelFormato);
        formatoPanel.add(comboFormato);
        panel.add(formatoPanel, gcFormato);

        // Botões
        var btnAlunos       = new JButton("Relatorio de Alunos");
        var btnEmpresas     = new JButton("Relatorio de Empresas");
        var btnVagas        = new JButton("Relatorio de Vagas");
        var btnCandidaturas = new JButton("Relatorio de Candidaturas");

        for (JButton b : new JButton[]{btnAlunos, btnEmpresas, btnVagas, btnCandidaturas}) {
            b.setFont(fontePadrao());
            b.setPreferredSize(new Dimension(260, 32));
        }

        btnAlunos.addActionListener(e       -> gerar("alunos"));
        btnEmpresas.addActionListener(e     -> gerar("empresas"));
        btnVagas.addActionListener(e        -> gerar("vagas"));
        btnCandidaturas.addActionListener(e -> gerar("candidaturas"));

        painelAdd(panel, btnAlunos,       0, 1);
        painelAdd(panel, btnEmpresas,     0, 2);
        painelAdd(panel, btnVagas,        0, 3);
        painelAdd(panel, btnCandidaturas, 0, 4);

        add(panel);
    }

    private void gerar(String tipo) {
        String formato  = (String) comboFormato.getSelectedItem();
        String extensao = formato.toLowerCase();
        String descricao = switch (formato) {
            case "CSV" -> "Arquivo CSV (*.csv)";
            case "PDF" -> "Arquivo PDF (*.pdf)";
            default    -> "Arquivo de texto (*.txt)";
        };

        var fc = new JFileChooser();
        fc.setFileFilter(new FileNameExtensionFilter(descricao, extensao));
        fc.setSelectedFile(new java.io.File("relatorio_" + tipo + "." + extensao));

        if (fc.showSaveDialog(this) != JFileChooser.APPROVE_OPTION) return;

        String caminho = fc.getSelectedFile().getAbsolutePath();
        // Garante extensao correta
        if (!caminho.toLowerCase().endsWith("." + extensao)) {
            caminho += "." + extensao;
        }

        try {
            String msg = switch (formato) {
                case "CSV" -> switch (tipo) {
                    case "alunos"       -> service.gerarCsvAlunos(caminho);
                    case "empresas"     -> service.gerarCsvEmpresas(caminho);
                    case "vagas"        -> service.gerarCsvVagas(caminho);
                    case "candidaturas" -> service.gerarCsvCandidaturas(caminho);
                    default -> "Tipo desconhecido";
                };
                case "PDF" -> switch (tipo) {
                    case "alunos"       -> service.gerarPdfAlunos(caminho);
                    case "empresas"     -> service.gerarPdfEmpresas(caminho);
                    case "vagas"        -> service.gerarPdfVagas(caminho);
                    case "candidaturas" -> service.gerarPdfCandidaturas(caminho);
                    default -> "Tipo desconhecido";
                };
                default -> switch (tipo) {
                    case "alunos"       -> service.gerarRelatorioAlunos(caminho);
                    case "empresas"     -> service.gerarRelatorioEmpresas(caminho);
                    case "vagas"        -> service.gerarRelatorioVagas(caminho);
                    case "candidaturas" -> service.gerarRelatorioCandidaturas(caminho);
                    default -> "Tipo desconhecido";
                };
            };
            JOptionPane.showMessageDialog(this, msg, "Sucesso", JOptionPane.INFORMATION_MESSAGE);
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Erro: " + ex.getMessage(), "Erro", JOptionPane.ERROR_MESSAGE);
        }
    }
}
