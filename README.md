# Portal de Estágios UniALFA 
Hackathon Institucional — Tecnologia em Sistemas para Internet, 3º Período — Faculdade UniALFA Umuarama

---

## Descrição do Problema

A Faculdade UniALFA não possuía um ambiente centralizado para conectar seus alunos às empresas da região que buscam estagiários. O processo era disperso e burocrático, dificultando tanto a divulgação de vagas pelas empresas quanto a candidatura dos alunos.

## Solução Desenvolvida

Um **Portal de Estágios distribuído em três módulos integrados**:

- **Back Office Java (UniALFA)** — Aplicação desktop para a equipe institucional gerenciar alunos, empresas, vagas e candidaturas, com geração de relatórios em TXT, CSV e PDF
- **API RESTful Node.js** — Motor central que processa toda a lógica de negócio, autenticação JWT, validações Zod e notificações automáticas
- **Portal Web PHP** — Interface para alunos (visualizar vagas, candidatar-se e acompanhar status) e empresas (gerenciar vagas e candidatos)

---

## Objetivos

- Permitir que empresas cadastrem e gerenciem vagas de estágio com facilidade
- Permitir que alunos visualizem vagas, se candidatem e acompanhem o andamento
- Notificar automaticamente o aluno a cada mudança de status na candidatura
- Centralizar o controle institucional no Back Office Java (aprovação de empresas, gestão de alunos)
- Garantir que o PHP nunca acesse o banco diretamente — toda comunicação passa pela API

---

## Arquitetura

```
┌──────────────────────┐
│   BACK OFFICE JAVA   │──── JDBC direto ────────────────────┐
│   (UniALFA Interno)  │                                     │
└──────────────────────┘                                     ▼
                                                    ┌─────────────────┐
┌──────────────────────┐   HTTP + JSON              │  MySQL 8        │
│   PORTAL WEB PHP     │◄──────────────────────────►│  portal_estagios│
│   (Alunos/Empresas)  │                            └────────▲────────┘
└──────────────────────┘                                     │
                                                    ORM (TypeORM)
                                            ┌────────────────┘
                                            │
                                   ┌────────────────┐
                                   │  API NODE.JS   │
                                   │  porta 3000    │
                                   │  Express + JWT │
                                   └────────────────┘
```

---

## Tecnologias Utilizadas

### API — Node.js
| Tecnologia | Uso |
|---|---|
| Node.js + TypeScript | Linguagem e runtime |
| Express 5 | Framework HTTP |
| TypeORM 0.3 | ORM, migrations e seeds |
| MySQL2 | Driver do banco |
| jsonwebtoken | Autenticação JWT |
| bcrypt | Hash de senhas |
| Zod | Validação de entrada |
| Nodemon + ts-node | Ambiente de desenvolvimento |

### Back Office — Java
| Tecnologia | Uso |
|---|---|
| Java 21 + Maven | Linguagem e build |
| Java Swing | Interface gráfica desktop |
| MySQL Connector/J 8.0.33 | Conexão JDBC |
| jBCrypt 0.4 | Hash bcrypt compatível com Node.js |
| iText 7.2.5 | Geração de relatórios PDF |
| Apache Commons CSV 1.10.0 | Geração de relatórios CSV |

### Portal Web — PHP
| Tecnologia | Uso |
|---|---|
| PHP 8+ | Linguagem server-side |
| cURL | Consumo HTTP da API Node.js |
| Bootstrap 5.3 | Estilização responsiva |
| Bootstrap Icons | Ícones |

### Banco de Dados
| Tecnologia | Uso |
|---|---|
| MySQL 8 | Banco relacional |
| Migrations TypeORM | Versionamento do schema |
| Seeds TypeORM | Dados iniciais de teste |

---

## Instalação e Execução

### Pré-requisitos

- Node.js 18+ com npm
- Java 21 com Maven
- PHP 8.1+ com extensão `curl` habilitada
- MySQL 8 rodando localmente
- Servidor PHP local (XAMPP, Laragon ou similar)

---

### 1. Banco de Dados

```sql
CREATE DATABASE portal_estagios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 2. API Node.js

```bash
cd portal-api
cp .env.example .env
```

Edite o `.env`:

```env
PORT=3000
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASS=sua_senha
DB_NAME=portal_estagios
JWT_SECRET=portal_estagios_secret_2026
JWT_EXPIRES_IN=1d
```

```bash
npm install
npm run migration:run
npm run seed
npm run dev
```

A API ficará disponível em `http://localhost:3000`.

**Dados de teste gerados pelo seed:**

| Tipo | Campo | Valor |
|---|---|---|
| Aluno | RA | `230001` |
| Aluno | Senha | `UniAlfa@2026` *(troca obrigatória no primeiro acesso)* |
| Empresa | E-mail | `techsolutions@empresa.com` |
| Empresa | Senha | `empresa123` |

---

### 3. Portal Web PHP

Aponte seu servidor local para a pasta `portal-php/` e acesse `http://localhost/portal-php/`.

> A API Node.js deve estar rodando na porta 3000.

---

### 4. Back Office Java

```bash
cd portal-java
mvn clean package
java -jar target/backoffice-unialfa.jar
```

| Campo | Valor |
|---|---|
| Usuário | `admin` |
| Senha | `admin123` |

> Edite a conexão com o banco em `src/main/java/com/unialfa/dao/Dao.java` se necessário.

---

## Estrutura do Projeto

```
portal-estagios-unialfa/
│
├── portal-api/                          # API RESTful Node.js + TypeScript
│   └── src/
│       ├── config/auth.ts               # Configuração JWT
│       ├── middleware/autenticacao.ts   # Middleware JWT
│       ├── models/                      # Entidades TypeORM (Aluno, Empresa, Vaga, Candidatura, Notificacao)
│       ├── repositories/                # Acesso a dados
│       ├── services/                    # Regras de negócio
│       ├── controllers/                 # Handlers HTTP com validação Zod
│       ├── routes/                      # Definição de endpoints
│       └── database/
│           ├── migrations/              # Versionamento do banco
│           └── seeds/run-seeds.ts       # Dados iniciais
│
├── portal-php/                          # Portal Web PHP OO
│   ├── Classe/                          # Modelagem de domínio
│   │   ├── EntidadeBase.php             # Classe abstrata base
│   │   ├── Aluno.php                    # Herda EntidadeBase
│   │   ├── Empresa.php                  # Herda EntidadeBase
│   │   ├── Vaga.php                     # Herda EntidadeBase
│   │   ├── Candidatura.php              # Herda EntidadeBase (constantes de status)
│   │   └── Painel.php                   # Cliente HTTP cURL para a API
│   ├── empresa/                         # Painel da Empresa
│   │   ├── loginEmpresa.php
│   │   ├── cadastroEmpresa.php
│   │   ├── inicioEmpresa.php            # Dashboard com estatísticas e notificações
│   │   ├── vagasEmpresa.php             # CRUD completo de vagas
│   │   ├── _form_vaga.php               # Partial reutilizável (criar/editar)
│   │   ├── candidatosVaga.php           # Candidatos por vaga com mini perfil
│   │   └── perfilEmpresa.php
│   ├── estudante/                       # Portal do Aluno
│   │   ├── loginEstudante.php           # Login por RA + senha
│   │   ├── trocarSenha.php              # Troca obrigatória no primeiro acesso
│   │   ├── inicioEstudante.php          # Dashboard com vagas e notificações
│   │   ├── vagasEstudante.php           # Listagem e candidatura
│   │   ├── candidaturasEstudante.php    # Acompanhamento com cancelamento
│   │   └── perfilEstudante.php          # Somente leitura
│   └── include/                         # Header, footer e menus
│
└── portal-java/                         # Back Office Java Desktop
    └── src/main/java/com/unialfa/
        ├── dao/         # Dao, AlunoDao, EmpresaDao, VagaDao, CandidaturaDao
        ├── model/       # Aluno, Empresa, Vaga, Candidatura
        ├── service/     # AlunoService, EmpresaService, VagaService, CandidaturaService, RelatorioService
        ├── gui/         # LoginGui, PrincipalGui, AlunoGui, EmpresaGui, VagaGui, CandidaturaGui, RelatorioGui
        └── Main.java
```

---

## Endpoints da API

### Sessão (público)
| Método | Rota | Descrição |
|---|---|---|
| POST | `/session/aluno` | Login com RA + senha |
| POST | `/session/empresa` | Login com e-mail + senha |

### Alunos
| Método | Rota | Auth |
|---|---|---|
| POST | `/alunos` | — |
| GET | `/alunos` | — |
| GET | `/alunos/perfil` | Aluno |
| PUT | `/alunos/trocar-senha` | Aluno |
| GET | `/alunos/:id` | — |
| PUT | `/alunos/:id` | Auth |
| DELETE | `/alunos/:id` | Auth |

### Empresas
| Método | Rota | Auth |
|---|---|---|
| POST | `/empresas` | — |
| GET | `/empresas` | — |
| GET | `/empresas/perfil` | Empresa |
| PUT | `/empresas/:id` | Auth |
| PATCH | `/empresas/:id/status` | Auth |
| DELETE | `/empresas/:id` | Auth |

### Vagas
| Método | Rota | Auth |
|---|---|---|
| GET | `/vagas` | — (aceita `?ativas=true`) |
| GET | `/vagas/minhas` | Empresa |
| GET | `/vagas/:id` | — |
| POST | `/vagas` | Empresa |
| PUT | `/vagas/:id` | Empresa |
| DELETE | `/vagas/:id` | Empresa |

### Candidaturas
| Método | Rota | Auth |
|---|---|---|
| GET | `/candidaturas/minhas` | Aluno |
| GET | `/candidaturas/vaga/:id` | Empresa |
| POST | `/candidaturas` | Aluno |
| PATCH | `/candidaturas/:id/status` | Empresa |
| PATCH | `/candidaturas/:id/cancelar` | Aluno |

### Notificações
| Método | Rota | Auth |
|---|---|---|
| GET | `/notificacoes` | Auth |
| PATCH | `/notificacoes/:id/lida` | Auth |

---

## Funcionalidades Implementadas

### Back Office Java
- [x] Login com autenticação (admin / admin123)
- [x] CRUD completo de alunos com controle de aptidão para estágio
- [x] Importação de alunos por arquivo `.txt` (formato: `ra;nome;email;curso;periodo`)
- [x] Senha padrão `UniAlfa@2026` gerada com jBCrypt em todos os alunos criados ou importados
- [x] Aprovação e bloqueio de empresas cadastradas
- [x] Consulta de vagas e candidaturas com visualização de status
- [x] Geração de relatórios em **TXT**, **CSV** e **PDF** para todas as entidades

### API Node.js
- [x] Arquitetura modular: Controllers → Services → Repositories
- [x] Autenticação JWT separada por tipo (aluno / empresa)
- [x] Validação de entrada com Zod em todos os endpoints
- [x] Migrations TypeORM para versionamento do banco
- [x] Seeds com dados de teste prontos
- [x] Notificações automáticas: nova candidatura (avisa empresa) e mudança de status (avisa aluno)
- [x] CRUD completo para todas as entidades
- [x] Respostas JSON padronizadas com HTTP codes semânticos

### Portal Web PHP
- [x] Modelagem OO: `EntidadeBase` abstrata → `Aluno`, `Empresa`, `Vaga`, `Candidatura`
- [x] `Painel.php` como única camada de comunicação HTTP com a API
- [x] Login do aluno por RA + senha, login da empresa por e-mail + senha
- [x] Troca de senha obrigatória no primeiro acesso (campo `primeiro_acesso`)
- [x] Listagem de vagas ativas com candidatura direta
- [x] Acompanhamento de status das candidaturas com opção de cancelar
- [x] Notificações exibidas no painel inicial de aluno e empresa
- [x] CRUD completo de vagas: criar, editar, encerrar, reativar e excluir
- [x] Candidatos por vaga com mini perfil expandível e atualização de status
- [x] Dados do aluno somente leitura na web (alterações apenas pelo Back Office)

---

## Integrantes da Equipe
| Joao Vitor Paiva Borges |
| Gabriel Priori de Morais |
| James Soares Silva |
|  |
|  |

---

## Evidências de Testes

### Fluxo do Aluno
1. Login com RA `230001` e senha `UniAlfa@2026`
2. Redirecionamento automático para troca de senha (primeiro acesso)
3. Nova senha criada → acesso liberado ao portal
4. Listagem das vagas ativas disponíveis
5. Candidatura realizada com confirmação
6. Status acompanhado em "Minhas Candidaturas"
7. Notificação recebida ao ter o status atualizado pela empresa

### Fluxo da Empresa
1. Cadastro no portal → aguarda aprovação da UniALFA
2. Aprovação realizada no Back Office Java
3. Login com e-mail e senha
4. Criação de nova vaga
5. Visualização dos candidatos com mini perfil expandível
6. Atualização de status do candidato → notificação enviada ao aluno
7. Edição, encerramento e exclusão de vaga

### Fluxo Back Office
1. Login como `admin` / `admin123`
2. Cadastro de aluno com senha padrão gerada automaticamente
3. Importação de alunos via arquivo `.txt`
4. Aprovação de empresa cadastrada
5. Consulta de vagas e candidaturas
6. Geração de relatório com seleção de formato (TXT, CSV ou PDF)

---

## Observações

- O Back Office Java acessa o banco diretamente via JDBC — é a camada institucional interna
- O Portal PHP nunca acessa o banco — toda operação passa pela API via HTTP
- O campo `primeiro_acesso` garante que qualquer aluno criado pelo Java troque a senha no primeiro login
- As notificações são criadas automaticamente nos dois eventos: nova candidatura (notifica empresa) e mudança de status (notifica aluno)
- Formato de importação `.txt`: `ra;nome;email;curso;periodo` — uma linha por aluno
