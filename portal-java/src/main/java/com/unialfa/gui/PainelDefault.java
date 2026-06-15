package com.unialfa.gui;

import javax.swing.*;
import java.awt.*;

public interface PainelDefault {

    default void painelAdd(JPanel panel, Component comp, int x, int y) {
        var gbc = new GridBagConstraints();
        gbc.gridx = x;
        gbc.gridy = y;
        gbc.insets = new Insets(4, 8, 4, 8);
        gbc.anchor = GridBagConstraints.WEST;
        gbc.fill = GridBagConstraints.HORIZONTAL;
        panel.add(comp, gbc);
    }

    default Font fontePadrao() { return new Font("Segoe UI", Font.PLAIN, 13); }
    default Font fonteTitulo() { return new Font("Segoe UI", Font.BOLD, 13); }
}
