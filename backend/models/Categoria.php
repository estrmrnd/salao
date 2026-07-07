<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Categoria
{
    public function listar($salao_id)
    {
        $pdo = conectar();

        $stmt = $pdo->prepare("
            SELECT id, nome
            FROM categorias
            WHERE salao_id = ?
            ORDER BY nome, id
        ");

        $stmt->execute([
            $salao_id
        ]);

        return $stmt->fetchAll();
    }

    public function criar($salao_id, $nome)
    {
        $pdo = conectar();

        $stmt = $pdo->prepare("
            INSERT INTO categorias 
            (salao_id, nome)
            VALUES (?, ?)
        ");

        return $stmt->execute([
            $salao_id,
            $nome
        ]);
    }

    public function atualizar($salao_id, $id, $nome)
    {
        $pdo = conectar();

        $stmt = $pdo->prepare("
            UPDATE categorias
            SET nome = ?
            WHERE id = ?
            AND salao_id = ?
        ");

        return $stmt->execute([
            $nome,
            $id,
            $salao_id
        ]);
    }

        public function excluir($salao_id, $id)
        {
            $pdo = conectar();

            $stmt = $pdo->prepare("
                DELETE FROM categorias
                WHERE id = ?
                AND salao_id = ?
            ");

            return $stmt->execute([
                $id,
                $salao_id
            ]);
        }
    }