package com.unialfa;

import com.unialfa.gui.LoginGui;

import javax.swing.*;

public class Main {
    public static void main(String[] args) throws Exception {
        UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        SwingUtilities.invokeLater(() -> {
            var login = new LoginGui();
            login.setVisible(true);
        });
    }
}
