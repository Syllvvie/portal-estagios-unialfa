package com.unialfa.gui;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;

public class LoginGui extends JFrame implements PainelDefault {

    private static final String USUARIO_ADMIN = "admin";
    private static final String SENHA_ADMIN   = "admin123";

    private final JTextField     campoUsuario = new JTextField(20);
    private final JPasswordField campoSenha   = new JPasswordField(20);
    private final JButton        btnEntrar    = new JButton("Entrar");
    private final JLabel         lblErro      = new JLabel(" ");

    public LoginGui() {
        setTitle("Back Office UniALFA — Login");
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setSize(340, 220);
        setLocationRelativeTo(null);
        setResizable(false);
        construirLayout();
    }

    private void construirLayout() {
        var panel = new JPanel(new GridBagLayout());
        panel.setBorder(BorderFactory.createEmptyBorder(20, 30, 20, 30));

        var titulo = new JLabel("Back Office UniALFA");
        titulo.setFont(new Font("Segoe UI", Font.BOLD, 15));
        var gcTitulo = new GridBagConstraints();
        gcTitulo.gridx = 0; gcTitulo.gridy = 0;
        gcTitulo.gridwidth = 2;
        gcTitulo.insets = new Insets(0, 0, 14, 0);
        panel.add(titulo, gcTitulo);

        campoUsuario.setFont(fontePadrao());
        campoSenha.setFont(fontePadrao());
        btnEntrar.setFont(fontePadrao());

        painelAdd(panel, new JLabel("Usuario:"), 0, 1);
        painelAdd(panel, campoUsuario, 1, 1);
        painelAdd(panel, new JLabel("Senha:"), 0, 2);
        painelAdd(panel, campoSenha, 1, 2);

        lblErro.setForeground(Color.RED);
        lblErro.setFont(new Font("Segoe UI", Font.PLAIN, 11));
        var gcErro = new GridBagConstraints();
        gcErro.gridx = 0; gcErro.gridy = 3; gcErro.gridwidth = 2;
        gcErro.insets = new Insets(2, 8, 2, 8);
        panel.add(lblErro, gcErro);

        var gcBtn = new GridBagConstraints();
        gcBtn.gridx = 0; gcBtn.gridy = 4; gcBtn.gridwidth = 2;
        gcBtn.fill = GridBagConstraints.HORIZONTAL;
        gcBtn.insets = new Insets(6, 8, 0, 8);
        panel.add(btnEntrar, gcBtn);

        btnEntrar.addActionListener(this::autenticar);
        getRootPane().setDefaultButton(btnEntrar);
        add(panel);
    }

    private void autenticar(ActionEvent e) {
        var usuario = campoUsuario.getText().trim();
        var senha   = new String(campoSenha.getPassword());

        if (usuario.equals(USUARIO_ADMIN) && senha.equals(SENHA_ADMIN)) {
            dispose();
            new PrincipalGui().setVisible(true);
        } else {
            lblErro.setText("Usuario ou senha invalidos.");
            campoSenha.setText("");
        }
    }
}
