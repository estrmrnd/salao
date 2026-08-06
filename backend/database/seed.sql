USE salao_db;

-- ==========================
-- SALÃO
-- ==========================

INSERT IGNORE INTO saloes (
    nome,
    slug,
    logo_url,
    telefone,
    email,
    endereco,
    ativo
)
VALUES (
    'Ticy Martins',
    'ticy-martins',
    NULL,
    '(21) 99999-9999',
    'contato@ticymartins.com',
    'Rio de Janeiro',
    1
);


-- ==========================
-- CATEGORIAS
-- ==========================

INSERT IGNORE INTO categorias (
    salao_id,
    nome
)
VALUES
(
    1,
    'Cabelos'
),
(
    1,
    'Sobrancelhas'
);


-- ==========================
-- SERVIÇOS
-- ==========================

INSERT IGNORE INTO servicos (
    salao_id,
    categoria_id,
    nome,
    descricao,
    preco,
    duracao_min,
    ativo
)
VALUES
(
    1,
    1,
    'Escova',
    'Escova modelada nos cabelos',
    80.00,
    60,
    1
),
(
    1,
    2,
    'Micropigmentação na sobrancelha',
    'Design e micropigmentação de sobrancelhas',
    250.00,
    120,
    1
);


-- ==========================
-- PROFISSIONAIS
-- ==========================

INSERT IGNORE INTO profissionais (
    salao_id,
    nome,
    especialidade,
    foto_url,
    ativo
)
VALUES
(
    1,
    'Ticy Martins',
    'Cabeleireira e designer de sobrancelhas',
    NULL,
    1
);


-- ==========================
-- RELAÇÃO PROFISSIONAL x SERVIÇO
-- ==========================

INSERT IGNORE INTO profissional_servico (
    profissional_id,
    servico_id
)
VALUES
(
    1,
    1
),
(
    1,
    2
);


-- ==========================
-- HORÁRIOS DE ATENDIMENTO
-- ==========================

INSERT IGNORE INTO horarios (
    profissional_id,
    dia_semana,
    inicio,
    fim
)
VALUES
(1, 1, '09:00', '18:00'),
(1, 2, '09:00', '18:00'),
(1, 3, '09:00', '18:00'),
(1, 4, '09:00', '18:00'),
(1, 5, '09:00', '18:00'),
(1, 6, '09:00', '14:00');


-- ==========================
-- CLIENTE TESTE
-- ==========================

INSERT IGNORE INTO clientes (
    nome,
    telefone,
    email,
    senha_hash
)
VALUES
(
    'Cliente Teste',
    '(21) 98888-8888',
    'cliente@email.com',
    '$2y$10$abcdefghijklmnopqrstuv'
);


-- ==========================
-- VINCULA CLIENTE AO SALÃO
-- ==========================

INSERT IGNORE INTO cliente_salao (
    cliente_id,
    salao_id
)
VALUES
(
    1,
    1
);


-- ==========================
-- ADMIN
-- ==========================

INSERT IGNORE INTO admins (
    nome,
    email,
    senha_hash,
    salao_id
)
VALUES
(
    'Administrador',
    'admin@salao.com',
    '$2y$10$abcdefghijklmnopqrstuv',
    1
);