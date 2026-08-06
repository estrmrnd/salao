CREATE DATABASE IF NOT EXISTS salao_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE salao_db;


-- =========================
-- SALÕES
-- =========================

CREATE TABLE IF NOT EXISTS saloes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo_url VARCHAR(500),
    telefone VARCHAR(50),
    email VARCHAR(255),
    endereco TEXT,
    ativo TINYINT(1) DEFAULT 1
);


-- =========================
-- CATEGORIAS
-- =========================

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salao_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,

    CONSTRAINT fk_categoria_salao
        FOREIGN KEY (salao_id)
        REFERENCES saloes(id)
        ON DELETE CASCADE
);


-- =========================
-- SERVIÇOS
-- =========================

CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salao_id INT NOT NULL,
    categoria_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) DEFAULT 0,
    duracao_min INT NOT NULL,
    ativo TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_servico_salao
        FOREIGN KEY (salao_id)
        REFERENCES saloes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_servico_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE CASCADE
);


-- =========================
-- PROFISSIONAIS
-- =========================

CREATE TABLE IF NOT EXISTS profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salao_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    especialidade VARCHAR(255),
    foto_url VARCHAR(500),
    ativo TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_profissional_salao
        FOREIGN KEY (salao_id)
        REFERENCES saloes(id)
        ON DELETE CASCADE
);


-- =========================
-- PROFISSIONAL x SERVIÇO
-- =========================

CREATE TABLE IF NOT EXISTS profissional_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    servico_id INT NOT NULL,

    UNIQUE KEY uk_profissional_servico (
        profissional_id,
        servico_id
    ),

    CONSTRAINT fk_ps_profissional
        FOREIGN KEY (profissional_id)
        REFERENCES profissionais(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_ps_servico
        FOREIGN KEY (servico_id)
        REFERENCES servicos(id)
        ON DELETE CASCADE
);


-- =========================
-- CLIENTES
-- =========================

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(50),
    email VARCHAR(255) UNIQUE,
    senha_hash VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================
-- CLIENTE x SALÃO
-- =========================

CREATE TABLE IF NOT EXISTS cliente_salao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    salao_id INT NOT NULL,

    UNIQUE KEY uk_cliente_salao (
        cliente_id,
        salao_id
    ),

    CONSTRAINT fk_cliente_salao_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cliente_salao_salao
        FOREIGN KEY (salao_id)
        REFERENCES saloes(id)
        ON DELETE CASCADE
);


-- =========================
-- HORÁRIOS
-- =========================

CREATE TABLE IF NOT EXISTS horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    dia_semana INT NOT NULL,
    inicio TIME NOT NULL,
    fim TIME NOT NULL,

    CONSTRAINT fk_horario_profissional
        FOREIGN KEY (profissional_id)
        REFERENCES profissionais(id)
        ON DELETE CASCADE
);


-- =========================
-- BLOQUEIOS
-- =========================

CREATE TABLE IF NOT EXISTS bloqueios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    data DATE NOT NULL,
    inicio TIME NOT NULL,
    fim TIME NOT NULL,
    motivo VARCHAR(255),

    CONSTRAINT fk_bloqueio_profissional
        FOREIGN KEY (profissional_id)
        REFERENCES profissionais(id)
        ON DELETE CASCADE
);


-- =========================
-- AGENDAMENTOS
-- =========================

CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    duracao_min INT NOT NULL,

    status ENUM(
        'pendente',
        'confirmado',
        'cancelado'
    ) DEFAULT 'pendente',

    observacao TEXT,

    CONSTRAINT fk_agendamento_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_agendamento_profissional
        FOREIGN KEY (profissional_id)
        REFERENCES profissionais(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_agendamento_servico
        FOREIGN KEY (servico_id)
        REFERENCES servicos(id)
        ON DELETE CASCADE
);

-- =========================
-- ADMINISTRADORES
-- =========================

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salao_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,

    CONSTRAINT fk_admin_salao
        FOREIGN KEY (salao_id)
        REFERENCES saloes(id)
        ON DELETE CASCADE
);