package com.unialfa.service;

import com.itextpdf.io.font.constants.StandardFonts;
import com.itextpdf.kernel.colors.ColorConstants;
import com.itextpdf.kernel.font.PdfFontFactory;
import com.itextpdf.kernel.pdf.PdfDocument;
import com.itextpdf.kernel.pdf.PdfWriter;
import com.itextpdf.layout.Document;
import com.itextpdf.layout.element.Cell;
import com.itextpdf.layout.element.Paragraph;
import com.itextpdf.layout.element.Table;
import com.itextpdf.layout.properties.TextAlignment;
import com.itextpdf.layout.properties.UnitValue;
import com.unialfa.model.*;
import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVPrinter;

import java.io.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.List;

/**
 * Serviço responsável por gerar relatórios em três formatos:
 *  - TXT  (formato tabular simples)
 *  - CSV  (compatível com Excel/LibreOffice)
 *  - PDF  (usando iText 7)
 */
public class RelatorioService {

    private final AlunoService       alunoService       = new AlunoService();
    private final EmpresaService     empresaService     = new EmpresaService();
    private final VagaService        vagaService        = new VagaService();
    private final CandidaturaService candidaturaService = new CandidaturaService();

    private static final DateTimeFormatter FMT  = DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm");
    private static final String            LINHA = "=".repeat(70);

    // ═══════════════════════════════ TXT ═════════════════════════════════════

    public String gerarRelatorioAlunos(String caminho) throws IOException {
        try (var pw = new PrintWriter(new FileWriter(caminho))) {
            cabecalhoTxt(pw, "ALUNOS");
            pw.printf("%-5s %-8s %-28s %-25s %-6s %-5s%n",
                "ID","RA","Nome","E-mail","Per.","Apto");
            pw.println("-".repeat(70));
            for (Aluno a : alunoService.listar()) {
                pw.printf("%-5d %-8s %-28s %-25s %-6s %-5s%n",
                    a.getId(), a.getRa(), a.getNome(), a.getEmail(),
                    a.getPeriodo() != null ? a.getPeriodo()+"o" : "-",
                    a.isApto() ? "Sim" : "Nao");
            }
            rodapeTxt(pw, alunoService.listar().size(), "aluno(s)");
        }
        return "Relatorio .txt gerado: " + caminho;
    }

    public String gerarRelatorioEmpresas(String caminho) throws IOException {
        try (var pw = new PrintWriter(new FileWriter(caminho))) {
            cabecalhoTxt(pw, "EMPRESAS");
            pw.printf("%-5s %-28s %-20s %-12s%n","ID","Nome","CNPJ","Status");
            pw.println("-".repeat(70));
            for (Empresa e : empresaService.listar()) {
                pw.printf("%-5d %-28s %-20s %-12s%n",
                    e.getId(), e.getNome(), e.getCnpj(), e.getStatusLabel());
            }
            rodapeTxt(pw, empresaService.listar().size(), "empresa(s)");
        }
        return "Relatorio .txt gerado: " + caminho;
    }

    public String gerarRelatorioVagas(String caminho) throws IOException {
        try (var pw = new PrintWriter(new FileWriter(caminho))) {
            cabecalhoTxt(pw, "VAGAS");
            pw.printf("%-5s %-28s %-22s %-8s%n","ID","Titulo","Empresa","Ativa");
            pw.println("-".repeat(70));
            for (Vaga v : vagaService.listar()) {
                pw.printf("%-5d %-28s %-22s %-8s%n",
                    v.getId(), v.getTitulo(),
                    v.getEmpresaNome() != null ? v.getEmpresaNome() : "-",
                    v.isAtiva() ? "Sim" : "Nao");
            }
            rodapeTxt(pw, vagaService.listar().size(), "vaga(s)");
        }
        return "Relatorio .txt gerado: " + caminho;
    }

    public String gerarRelatorioCandidaturas(String caminho) throws IOException {
        try (var pw = new PrintWriter(new FileWriter(caminho))) {
            cabecalhoTxt(pw, "CANDIDATURAS");
            pw.printf("%-5s %-22s %-8s %-22s %-14s%n",
                "ID","Aluno","RA","Vaga","Status");
            pw.println("-".repeat(70));
            for (Candidatura c : candidaturaService.listar()) {
                pw.printf("%-5d %-22s %-8s %-22s %-14s%n",
                    c.getId(),
                    c.getAlunoNome()  != null ? c.getAlunoNome()  : "-",
                    c.getAlunoRa()    != null ? c.getAlunoRa()    : "-",
                    c.getVagaTitulo() != null ? c.getVagaTitulo() : "-",
                    c.getStatusLabel());
            }
            rodapeTxt(pw, candidaturaService.listar().size(), "candidatura(s)");
        }
        return "Relatorio .txt gerado: " + caminho;
    }

    // ═══════════════════════════════ CSV ═════════════════════════════════════

    public String gerarCsvAlunos(String caminho) throws IOException {
        var formato = CSVFormat.Builder.create(CSVFormat.EXCEL)
            .setHeader("ID","RA","Nome","E-mail","Curso","Periodo","Apto","Ativo")
            .setDelimiter(';').build();
        try (var pw = new PrintWriter(new OutputStreamWriter(new FileOutputStream(caminho), "UTF-8"));
             var csv = new CSVPrinter(pw, formato)) {
            for (Aluno a : alunoService.listar()) {
                csv.printRecord(
                    a.getId(), a.getRa(), a.getNome(), a.getEmail(),
                    a.getCurso() != null ? a.getCurso() : "",
                    a.getPeriodo() != null ? a.getPeriodo() : "",
                    a.isApto() ? "Sim" : "Nao",
                    a.isAtivo() ? "Ativo" : "Inativo");
            }
        }
        return "Relatorio .csv gerado: " + caminho;
    }

    public String gerarCsvEmpresas(String caminho) throws IOException {
        var formato = CSVFormat.Builder.create(CSVFormat.EXCEL)
            .setHeader("ID","Nome","CNPJ","E-mail","Telefone","Area","Status")
            .setDelimiter(';').build();
        try (var pw = new PrintWriter(new OutputStreamWriter(new FileOutputStream(caminho), "UTF-8"));
             var csv = new CSVPrinter(pw, formato)) {
            for (Empresa e : empresaService.listar()) {
                csv.printRecord(
                    e.getId(), e.getNome(), e.getCnpj(), e.getEmail(),
                    e.getTelefone() != null ? e.getTelefone() : "",
                    e.getAreaAtuacao() != null ? e.getAreaAtuacao() : "",
                    e.getStatusLabel());
            }
        }
        return "Relatorio .csv gerado: " + caminho;
    }

    public String gerarCsvVagas(String caminho) throws IOException {
        var formato = CSVFormat.Builder.create(CSVFormat.EXCEL)
            .setHeader("ID","Titulo","Empresa","Area","Bolsa","Carga (h)","Ativa")
            .setDelimiter(';').build();
        try (var pw = new PrintWriter(new OutputStreamWriter(new FileOutputStream(caminho), "UTF-8"));
             var csv = new CSVPrinter(pw, formato)) {
            for (Vaga v : vagaService.listar()) {
                csv.printRecord(
                    v.getId(), v.getTitulo(),
                    v.getEmpresaNome() != null ? v.getEmpresaNome() : "-",
                    v.getArea() != null ? v.getArea() : "",
                    v.getBolsa() != null ? String.format("%.2f", v.getBolsa()) : "",
                    v.getCargaHoraria() != null ? v.getCargaHoraria() : "",
                    v.isAtiva() ? "Sim" : "Nao");
            }
        }
        return "Relatorio .csv gerado: " + caminho;
    }

    public String gerarCsvCandidaturas(String caminho) throws IOException {
        var formato = CSVFormat.Builder.create(CSVFormat.EXCEL)
            .setHeader("ID","Aluno","RA","Vaga","Empresa","Status","Data")
            .setDelimiter(';').build();
        try (var pw = new PrintWriter(new OutputStreamWriter(new FileOutputStream(caminho), "UTF-8"));
             var csv = new CSVPrinter(pw, formato)) {
            for (Candidatura c : candidaturaService.listar()) {
                csv.printRecord(
                    c.getId(),
                    c.getAlunoNome()  != null ? c.getAlunoNome()  : "-",
                    c.getAlunoRa()    != null ? c.getAlunoRa()    : "-",
                    c.getVagaTitulo() != null ? c.getVagaTitulo() : "-",
                    c.getEmpresaNome()!= null ? c.getEmpresaNome(): "-",
                    c.getStatusLabel(),
                    c.getCreatedAt()  != null ? c.getCreatedAt().substring(0,10) : "-");
            }
        }
        return "Relatorio .csv gerado: " + caminho;
    }

    // ═══════════════════════════════ PDF ═════════════════════════════════════

    public String gerarPdfAlunos(String caminho) throws Exception {
        try (var doc = abrirPdf(caminho)) {
            tituloPdf(doc, "Relatorio de Alunos");
            var tabela = new Table(UnitValue.createPercentArray(new float[]{5,10,25,25,12,8,8}))
                .useAllAvailableWidth();
            cabecalhoPdf(tabela, "ID","RA","Nome","E-mail","Curso","Per.","Apto");
            for (Aluno a : alunoService.listar()) {
                linhaPdf(tabela,
                    str(a.getId()), a.getRa(), a.getNome(), a.getEmail(),
                    a.getCurso() != null ? a.getCurso() : "-",
                    a.getPeriodo() != null ? a.getPeriodo()+"o" : "-",
                    a.isApto() ? "Sim" : "Nao");
            }
            doc.add(tabela);
            rodapePdf(doc, alunoService.listar().size(), "aluno(s)");
        }
        return "Relatorio .pdf gerado: " + caminho;
    }

    public String gerarPdfEmpresas(String caminho) throws Exception {
        try (var doc = abrirPdf(caminho)) {
            tituloPdf(doc, "Relatorio de Empresas");
            var tabela = new Table(UnitValue.createPercentArray(new float[]{5,28,20,25,15}))
                .useAllAvailableWidth();
            cabecalhoPdf(tabela, "ID","Nome","CNPJ","E-mail","Status");
            for (Empresa e : empresaService.listar()) {
                linhaPdf(tabela,
                    str(e.getId()), e.getNome(), e.getCnpj(), e.getEmail(), e.getStatusLabel());
            }
            doc.add(tabela);
            rodapePdf(doc, empresaService.listar().size(), "empresa(s)");
        }
        return "Relatorio .pdf gerado: " + caminho;
    }

    public String gerarPdfVagas(String caminho) throws Exception {
        try (var doc = abrirPdf(caminho)) {
            tituloPdf(doc, "Relatorio de Vagas");
            var tabela = new Table(UnitValue.createPercentArray(new float[]{5,30,25,15,15,10}))
                .useAllAvailableWidth();
            cabecalhoPdf(tabela, "ID","Titulo","Empresa","Area","Bolsa","Ativa");
            for (Vaga v : vagaService.listar()) {
                linhaPdf(tabela,
                    str(v.getId()), v.getTitulo(),
                    v.getEmpresaNome() != null ? v.getEmpresaNome() : "-",
                    v.getArea() != null ? v.getArea() : "-",
                    v.getBolsa() != null ? "R$ "+String.format("%.2f",v.getBolsa()) : "-",
                    v.isAtiva() ? "Sim" : "Nao");
            }
            doc.add(tabela);
            rodapePdf(doc, vagaService.listar().size(), "vaga(s)");
        }
        return "Relatorio .pdf gerado: " + caminho;
    }

    public String gerarPdfCandidaturas(String caminho) throws Exception {
        try (var doc = abrirPdf(caminho)) {
            tituloPdf(doc, "Relatorio de Candidaturas");
            var tabela = new Table(UnitValue.createPercentArray(new float[]{5,22,10,22,20,16}))
                .useAllAvailableWidth();
            cabecalhoPdf(tabela, "ID","Aluno","RA","Vaga","Empresa","Status");
            for (Candidatura c : candidaturaService.listar()) {
                linhaPdf(tabela,
                    str(c.getId()),
                    c.getAlunoNome()  != null ? c.getAlunoNome()  : "-",
                    c.getAlunoRa()    != null ? c.getAlunoRa()    : "-",
                    c.getVagaTitulo() != null ? c.getVagaTitulo() : "-",
                    c.getEmpresaNome()!= null ? c.getEmpresaNome(): "-",
                    c.getStatusLabel());
            }
            doc.add(tabela);
            rodapePdf(doc, candidaturaService.listar().size(), "candidatura(s)");
        }
        return "Relatorio .pdf gerado: " + caminho;
    }

    // ═══════════════════════════════ HELPERS TXT ═════════════════════════════

    private void cabecalhoTxt(PrintWriter pw, String tipo) {
        pw.println(LINHA);
        pw.println("RELATORIO DE " + tipo + " - Portal de Estagios UniALFA");
        pw.println("Gerado em: " + LocalDateTime.now().format(FMT));
        pw.println(LINHA);
    }

    private void rodapeTxt(PrintWriter pw, int total, String unidade) {
        pw.println(LINHA);
        pw.println("Total: " + total + " " + unidade);
    }

    // ═══════════════════════════════ HELPERS PDF ═════════════════════════════

    private Document abrirPdf(String caminho) throws Exception {
        var writer = new PdfWriter(caminho);
        var pdf    = new PdfDocument(writer);
        return new Document(pdf);
    }

    private void tituloPdf(Document doc, String titulo) throws Exception {
        var fonte = PdfFontFactory.createFont(StandardFonts.HELVETICA_BOLD);
        doc.add(new Paragraph("Portal de Estagios UniALFA")
            .setFont(fonte).setFontSize(9).setFontColor(ColorConstants.GRAY));
        doc.add(new Paragraph(titulo)
            .setFont(fonte).setFontSize(14).setTextAlignment(TextAlignment.CENTER)
            .setMarginBottom(4));
        doc.add(new Paragraph("Gerado em: " + LocalDateTime.now().format(FMT))
            .setFontSize(8).setFontColor(ColorConstants.GRAY)
            .setTextAlignment(TextAlignment.CENTER).setMarginBottom(10));
    }

    private void cabecalhoPdf(Table tabela, String... colunas) throws Exception {
        var fonte = PdfFontFactory.createFont(StandardFonts.HELVETICA_BOLD);
        for (String col : colunas) {
            tabela.addHeaderCell(new Cell()
                .add(new Paragraph(col).setFont(fonte).setFontSize(9))
                .setBackgroundColor(ColorConstants.LIGHT_GRAY)
                .setTextAlignment(TextAlignment.CENTER));
        }
    }

    private void linhaPdf(Table tabela, String... valores) {
        for (String val : valores) {
            tabela.addCell(new Cell()
                .add(new Paragraph(val != null ? val : "-").setFontSize(8))
                .setTextAlignment(TextAlignment.LEFT));
        }
    }

    private void rodapePdf(Document doc, int total, String unidade) throws Exception {
        var fonte = PdfFontFactory.createFont(StandardFonts.HELVETICA);
        doc.add(new Paragraph("\nTotal: " + total + " " + unidade)
            .setFont(fonte).setFontSize(9).setFontColor(ColorConstants.GRAY));
    }

    private String str(Object o) { return o != null ? o.toString() : "-"; }
}
