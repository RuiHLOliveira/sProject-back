create database fluxodecaixa;


drop table if exists movimentos;
drop table if exists classes;
drop table if exists tiposmovimentos;
drop table if exists contas;

CREATE TABLE contas (
    id serial primary KEY,
    nome varchar(255),
    saldo decimal(10,2),
    created_at timestamp,
    updated_at timestamp,
    deleted_at timestamp
);

CREATE TABLE tiposmovimentos (
    id serial primary KEY,
    nome varchar(255),
    created_at timestamp,
    updated_at timestamp,
    deleted_at timestamp
);

CREATE TABLE classes (
    id serial primary KEY,
    nome varchar(255),
    created_at timestamp,
    updated_at timestamp,
    deleted_at timestamp
);

CREATE TABLE movimentos (
    id serial primary KEY,
    descricao text,
    valor decimal(10,2),
    dataMovimento timestamp,
    classe integer,
    tipomovimento integer,
    conta integer,
    created_at timestamp,
    updated_at timestamp,
    deleted_at timestamp,
    FOREIGN KEY (tipomovimento) REFERENCES tiposmovimentos (id),
    FOREIGN KEY (conta) REFERENCES contas (id),
    FOREIGN KEY (classe) REFERENCES classes (id)
);


truncate table movimentos;
truncate table classes cascade;
truncate table tiposmovimentos cascade;
truncate table contas cascade;


INSERT INTO contas (id,nome,saldo,created_at,updated_at,deleted_at) values
(1,'Itau',0.00,'2022-06-10 00:00:00',NULL,NULL),
(2,'NuConta',0.00,'2022-06-10 00:00:00',NULL,NULL),
(3,'Nubank Cartao',0.00,'2022-06-10 00:00:00',NULL,NULL);

INSERT INTO classes (id,nome,created_at,updated_at,deleted_at) VALUES
(1,'Assinaturas','2022-06-10 00:00:00',NULL,NULL),
(2,'Parcelamentos','2022-06-10 00:00:00',NULL,NULL),
(3,'Compras','2022-06-10 00:00:00',NULL,NULL),
(4,'Rendimento','2022-06-10 00:00:00',NULL,NULL),

INSERT INTO tiposmovimentos (id,nome,created_at,updated_at,deleted_at) VALUES
(1,'Farmácia Medicação','2022-06-10 00:00:00',NULL,NULL),
(2,'Lazer','2022-06-10 00:00:00',NULL,NULL),
(3,'Educacao','2022-06-10 00:00:00',NULL,NULL),
(4,'Casa','2022-06-10 00:00:00',NULL,NULL),
(5,'Beleza','2022-06-10 00:00:00',NULL,NULL),
(6,'Presente','2022-06-10 00:00:00',NULL,NULL),
(7,'Farmácia pessoal','2022-06-10 00:00:00',NULL,NULL),
(8,'Mercado','2022-06-10 00:00:00',NULL,NULL),
(9,'Trasporte','2022-06-10 00:00:00',NULL,NULL),
(10,'Rendimentos','2022-06-10 00:00:00',NULL,NULL),
(11,'Salario','2022-06-10 00:00:00',NULL,NULL),
(12,'Saque','2022-06-10 00:00:00',NULL,NULL);

--conta nubank cartao maio
INSERT INTO movimentos (datamovimento,conta,tipomovimento,classe,created_at,valor,descricao) VALUES
('2022-05-09 00:00:00', 3, 3, 1, '2022-06-14 00:00:00', -56.25,'Alura'),
('2022-05-09 00:00:00', 3, 4, 2, '2022-06-14 00:00:00', -198.24,'ec budcomercio geladeira'),
('2022-05-09 00:00:00', 3, 2, 3, '2022-06-14 00:00:00', -32.93,'uber'),
('2022-05-09 00:00:00', 3, 4, 2, '2022-06-14 00:00:00', -48.46,'casas bahia'),
('2022-05-09 00:00:00', 3, 4, 2, '2022-06-14 00:00:00', -143.09,'carrefour microondas maquina'),
('2022-05-09 00:00:00', 3, 4, 2, '2022-06-14 00:00:00', -48.15,'casas bahia'),
('2022-05-09 00:00:00', 3, 4, 2, '2022-06-14 00:00:00', -198.38,'casas bahia'),
('2022-05-09 00:00:00', 3, 3, 1, '2022-06-14 00:00:00', -59.90,'PPR old'),
('2022-05-10 00:00:00', 3, 3, 1, '2022-06-14 00:00:00', -48.90,'PPR new'),
('2022-05-11 00:00:00', 3, 5, 2, '2022-06-14 00:00:00', -83.34,'manual'),
('2022-05-12 00:00:00', 3, 4, 3, '2022-06-14 00:00:00', -78.00, 'amazon mop + 2 livros'),
('2022-05-12 00:00:00', 3, 3, 3, '2022-06-14 00:00:00', -35.00, 'livro medalha milagrosa (eu)'),
('2022-05-12 00:00:00', 3, 6, 3, '2022-06-14 00:00:00', -35.00, 'livro medalha milagrosa (presente)'),
('2022-05-12 00:00:00', 3, 7, 3, '2022-06-14 00:00:00', -32.46, 'amazon - escova de dente'),
('2022-05-13 00:00:00', 3, 8, 3, '2022-06-14 00:00:00', -24.97, 'ifood mercado'),
('2022-05-14 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -10.92, 'uber let>rui'),
('2022-05-15 00:00:00', 3, 4, 3, '2022-06-14 00:00:00',  12.02, 'amazon - luvas'),
('2022-05-16 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -10.98, 'uber let>rui'),
('2022-05-16 00:00:00', 3, 2, 1, '2022-06-14 00:00:00', -39.90, 'netflix'),
('2022-05-17 00:00:00', 3, 2, 1, '2022-06-14 00:00:00', -19.90, 'spotify'),
('2022-05-18 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -14.98, 'uber madu>rui'),
('2022-05-18 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -13.95, 'uber rui>madu'),
('2022-05-19 00:00:00', 3, 4, 3, '2022-06-14 00:00:00', -59.90, 'amazon - filtro iclamper'),
('2022-05-23 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -13.01, 'uber let>rui'),
('2022-05-24 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -14.97, 'uber amparo>rui'),
('2022-05-24 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -14.98, 'uber rui>amparo'),
('2022-05-24 00:00:00', 3, 3, 1, '2022-06-14 00:00:00', -19.90, 'amazon book'),
('2022-05-25 00:00:00', 3, 3, 2, '2022-06-14 00:00:00', -113.91, 'mestres do capitalismo'),
('2022-05-29 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -19.94, 'uber rui>let>sula'),
('2022-05-29 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -24.99, 'uber sula>let>rui'),
('2022-05-30 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -12.91, 'uber let>rui'),
('2022-05-30 00:00:00', 3, 2, 1, '2022-06-14 00:00:00', -9.90, 'amazon prime'),
('2022-06-03 00:00:00', 3, 7, 3, '2022-06-14 00:00:00', -81.73, 'ml mascaras'),
('2022-06-06 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -14.97, 'uber rui>amparo'),
('2022-06-06 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -14.97, 'uber amparo>bebel'),
('2022-06-06 00:00:00', 3, 9, 3, '2022-06-14 00:00:00', -9.95, 'uber let>rui');

--conta itau maio
INSERT INTO movimentos (datamovimento,conta,tipomovimento,classe,created_at,valor,descricao) VALUES
('2022-05-01 00:00:00' , 1, 10, 0, '2022-06-14 00:00:00', 943.40, 'Saldo Inicial'),
('2022-06-03 00:00:00' , 1, 12, 3, '2022-06-14 00:00:00', -200.00, 'saque'),
('2022-06-03 00:00:00' , 1, 10, 4, '2022-06-14 00:00:00', 0.14, 'rendimento'),
('2022-06-10 00:00:00' , 1, 4, 3, '2022-06-14 00:00:00', -186.65, 'net ref abril'),
('2022-06-15 00:00:00' , 1, 10, 4, '2022-06-14 00:00:00', 0.08, 'rendimento'),
('2022-05-17 00:00:00' , 1, 4, 3, '2022-06-14 00:00:00', -5.00, 'rshop-mp *conectr-17/05'),
('2022-05-31 00:00:00' , 1, 11, 4, '2022-06-14 00:00:00', 3778.68, 'salario');








