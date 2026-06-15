package com.unialfa.dao;

import java.sql.Connection;
import java.sql.DriverManager;

public class Dao {

    private Connection connection;

    private static final String URL  = "jdbc:mysql://localhost:3306/portal_estagios?useTimezone=true&serverTimezone=UTC";
    private static final String USER = "root";
    private static final String PASS = "";

    public Dao() {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            this.connection = DriverManager.getConnection(URL, USER, PASS);
        } catch (Exception e) {
            System.err.println("Erro ao conectar ao banco: " + e.getMessage());
        }
    }

    public Connection getConnection() {
        return connection;
    }
}
