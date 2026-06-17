package com.unialfa.gui;

import com.unialfa.service.*;

import javax.swing.*;
import java.awt.*;

public class PrincipalGui extends JFrame implements PainelDefault {

    private final AlunoGui       alunoGui;
    private final EmpresaGui     empresaGui;
    private final VagaGui        vagaGui;
    private final CandidaturaGui candidaturaGui;
    private final RelatorioGui   relatorioGui;

    public PrincipalGui() {
        setTitle("Back Office — Portal de Estagios UniALFA");
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setExtendedState(MAXIMIZED_BOTH);
        setSize(1000, 650);
        setLocationRelativeTo(null);

        alunoGui       = new AlunoGui(new AlunoService());
        empresaGui     = new EmpresaGui(new EmpresaService());
        vagaGui        = new VagaGui(new VagaService());
        candidaturaGui = new CandidaturaGui(new CandidaturaService());
        relatorioGui   = new RelatorioGui(new RelatorioService());

        setJMenuBar(construirMenuBar());

        // Painel central simples com label de boas-vindas
        var centro = new JPanel(new BorderLayout());
        var lbl = new JLabel("Selecione uma opcao no menu acima.", SwingConstants.CENTER);
        lbl.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        centro.add(lbl, BorderLayout.CENTER);
        add(centro);
    }

    private JMenuBar construirMenuBar() {
        var menuBar = new JMenuBar();

        menuBar.add(menuItem("Alunos",       () -> alunoGui.setVisible(true)));
        menuBar.add(menuItem("Empresas",     () -> empresaGui.setVisible(true)));
        menuBar.add(menuItem("Vagas",        () -> vagaGui.setVisible(true)));
        menuBar.add(menuItem("Candidaturas", () -> candidaturaGui.setVisible(true)));
        menuBar.add(menuItem("Relatorios",   () -> relatorioGui.setVisible(true)));

        return menuBar;
    }

    private JMenu menuItem(String texto, Runnable acao) {
        var menu = new JMenu(texto);
        menu.setFont(new Font("Segoe UI", Font.PLAIN, 13));
        menu.addMouseListener(new java.awt.event.MouseAdapter() {
            @Override
            public void mouseClicked(java.awt.event.MouseEvent e) {
                acao.run();
            }
        });
        return menu;
    }
}
