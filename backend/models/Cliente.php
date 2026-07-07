<?php

require_once '../config/headers.php';
require_once '../config/database.php';

class Cliente
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = conectar();
    }

    public function criar($salao_id, $nome, $telefone, $email)
    {
        $cliente = null;

        if ($email) {

            $stmt = $this->pdo->prepare("
                SELECT id
                FROM clientes
                WHERE email = ?
            ");

            $stmt->execute([$email]);

            $cliente = $stmt->fetch();
        }

        if ($cliente) {

            $this->pdo->prepare("
                UPDATE clientes
                SET
                    nome = ?,
                    telefone = ?
                WHERE id = ?
            ")->execute([
                $nome,
                $telefone,
                $cliente['id']
            ]);

            $cliente_id = $cliente['id'];

            $existente = true;

        } else {

            $stmt = $this->pdo->prepare("
                INSERT INTO clientes (
                    nome,
                    telefone,
                    email,
                    senha_hash
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $nome,
                $telefone,
                $email ?: null,
                password_hash(uniqid(), PASSWORD_DEFAULT)
            ]);

            $cliente_id = $this->pdo->lastInsertId();

            $existente = false;
        }

        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM cliente_salao
            WHERE cliente_id = ?
            AND salao_id = ?
        ");

        $stmt->execute([
            $cliente_id,
            $salao_id
        ]);

        if (!$stmt->fetch()) {

            $stmt = $this->pdo->prepare("
                INSERT INTO cliente_salao (
                    cliente_id,
                    salao_id
                )
                VALUES (?, ?)
            ");

            $stmt->execute([
                $cliente_id,
                $salao_id
            ]);
        }

        return [
            'id' => (int)$cliente_id,
            'existente' => $existente
        ];
    }

    public function buscar($salao_id, $id = null, $email = null)
    {
        if ($id) {

            $stmt = $this->pdo->prepare("
                SELECT
                    c.id,
                    c.nome,
                    c.telefone,
                    c.email,
                    c.criado_em
                FROM clientes c
                INNER JOIN cliente_salao cs
                    ON cs.cliente_id = c.id
                WHERE
                    c.id = ?
                AND
                    cs.salao_id = ?
            ");

            $stmt->execute([
                $id,
                $salao_id
            ]);

            return $stmt->fetch();
        }

        if ($email) {

            $stmt = $this->pdo->prepare("
                SELECT
                    c.id,
                    c.nome,
                    c.telefone,
                    c.email,
                    c.criado_em
                FROM clientes c
                INNER JOIN cliente_salao cs
                    ON cs.cliente_id = c.id
                WHERE
                    c.email = ?
                AND
                    cs.salao_id = ?
            ");

            $stmt->execute([
                $email,
                $salao_id
            ]);

            return $stmt->fetch();
        }

        return null;
    }

    public function listar($salao_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.nome,
                c.telefone,
                c.email,
                c.criado_em
            FROM clientes c
            INNER JOIN cliente_salao cs
                ON cs.cliente_id = c.id
            WHERE cs.salao_id = ?
            ORDER BY c.nome
        ");

        $stmt->execute([
            $salao_id
        ]);

        return $stmt->fetchAll();
    }
}