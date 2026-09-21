CREATE DATABASE IF NOT EXISTS tony_baterias
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE tony_baterias;

CREATE TABLE usuarios (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL UNIQUE,
    senha           VARCHAR(255)        NOT NULL,
    perfil          ENUM('admin', 'vendedor') NOT NULL DEFAULT 'vendedor',
    ativo           TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(100)        NOT NULL UNIQUE,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE fornecedores (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(150)        NOT NULL,
    contato         VARCHAR(100)        NULL,
    cnpj            VARCHAR(18)         NULL,
    ativo           TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clientes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(150)        NOT NULL,
    cpf             VARCHAR(14)         NULL UNIQUE,
    cnpj            VARCHAR(18)         NULL UNIQUE,
    telefone        VARCHAR(20)         NULL,
    email           VARCHAR(150)        NULL,
    endereco        VARCHAR(255)        NULL,
    ativo           TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE produtos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id    INT UNSIGNED        NULL,
    nome            VARCHAR(150)        NOT NULL,
    descricao       TEXT                NULL,
    marca           VARCHAR(80)         NULL,
    sku             VARCHAR(40)         NULL UNIQUE,
    preco           DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    garantia_meses  SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    estoque_atual   INT                 NOT NULL DEFAULT 0,
    estoque_minimo  INT                 NOT NULL DEFAULT 0,
    ativo           TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_produtos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE movimentacoes_estoque (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produto_id      INT UNSIGNED        NOT NULL,
    usuario_id      INT UNSIGNED        NOT NULL,
    fornecedor_id   INT UNSIGNED        NULL,
    tipo            ENUM('entrada', 'saida') NOT NULL,
    quantidade      INT UNSIGNED        NOT NULL,
    observacao      VARCHAR(255)        NULL,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_mov_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_mov_fornecedor
        FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT UNSIGNED        NULL,
    usuario_id      INT UNSIGNED        NOT NULL,
    status          ENUM('pendente', 'concluido', 'cancelado')
                                         NOT NULL DEFAULT 'pendente',
    forma_pagamento ENUM('dinheiro', 'pix', 'cartao_credito', 'cartao_debito', 'boleto') NULL,
    valor_total     DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    observacao      VARCHAR(255)        NULL,
    criado_em       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE itens_pedido (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT UNSIGNED        NOT NULL,
    produto_id      INT UNSIGNED        NOT NULL,
    quantidade      INT UNSIGNED        NOT NULL,
    preco_unitario  DECIMAL(10,2)       NOT NULL,
    CONSTRAINT fk_itens_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_itens_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_produtos_nome        ON produtos(nome);
CREATE INDEX idx_clientes_nome        ON clientes(nome);
CREATE INDEX idx_fornecedores_nome    ON fornecedores(nome);
CREATE INDEX idx_mov_produto_data     ON movimentacoes_estoque(produto_id, criado_em);
CREATE INDEX idx_pedidos_cliente      ON pedidos(cliente_id);
CREATE INDEX idx_pedidos_status_data  ON pedidos(status, criado_em);
CREATE INDEX idx_itens_pedido         ON itens_pedido(pedido_id);

INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador', 'admin@tonybaterias.com', '123456', 'admin');

INSERT INTO categorias (nome) VALUES
('Baterias automotivas'),
('Baterias moto'),
('Acessórios'),
('Alimentos');

INSERT INTO fornecedores (nome, contato, cnpj) VALUES
('Heliar Distribuidora SP', '(11) 3456-7890', '12.345.678/0001-90'),
('Acdelco Centro-Oeste', '(11) 2345-6789', '23.456.789/0001-01'),
('Distribuidora Pururuca Ltda', '(11) 4567-8901', NULL);

INSERT INTO produtos (categoria_id, nome, descricao, marca, sku, preco, garantia_meses, estoque_atual, estoque_minimo) VALUES
(1, 'Acdelco 50Ah', 'Bateria Acdelco 50Ah, ideal para carros de passeio', 'Acdelco', 'BAT-ACD-50', 320.00, 12, 8, 3),
(1, 'Acdelco 60Ah', 'Bateria Acdelco 60Ah, alta durabilidade', 'Acdelco', 'BAT-ACD-60', 380.00, 12, 5, 3),
(1, 'Bateria Semi-Nova 45Ah', 'Bateria seminova revisada, 45Ah', NULL, 'BAT-SNV-45', 150.00, 3, 2, 2),
(1, 'Bateria Semi-Nova 60Ah', 'Bateria seminova revisada, 60Ah', NULL, 'BAT-SNV-60', 150.00, 3, 4, 2),
(1, 'Heliar 60Ah', 'Bateria Heliar 60Ah original', 'Heliar', 'BAT-HEL-60', 410.00, 18, 6, 3),
(4, 'Pururuca Tradicional', 'Petisco tradicional vendido no balcão', NULL, NULL, 25.00, 0, 15, 5);

INSERT INTO movimentacoes_estoque (produto_id, usuario_id, fornecedor_id, tipo, quantidade, observacao) VALUES
(1, 1, 2,    'entrada', 8,  'Estoque inicial (carga de dados)'),
(2, 1, 2,    'entrada', 5,  'Estoque inicial (carga de dados)'),
(3, 1, NULL, 'entrada', 2,  'Estoque inicial (carga de dados)'),
(4, 1, NULL, 'entrada', 4,  'Estoque inicial (carga de dados)'),
(5, 1, 1,    'entrada', 6,  'Estoque inicial (carga de dados)'),
(6, 1, 3,    'entrada', 15, 'Estoque inicial (carga de dados)');

INSERT INTO clientes (nome, cpf, telefone, email) VALUES
('João Pereira', '111.222.333-44', '(11) 98888-1111', 'joao.pereira@example.com'),
('Oficina Mecânica Silva', NULL, '(11) 97777-2222', 'contato@oficinasilva.example.com');
