<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Servico
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = conectar();
    }

    public function listar(int $salao_id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                s.id,
                s.nome,
                s.descricao,
                s.preco,
                s.duracao_min,
                s.ativo,
                s.categoria_id,
                c.nome AS categoria
            FROM servicos s
            INNER JOIN categorias c
                ON c.id = s.categoria_id
            WHERE s.salao_id = ?
            ORDER BY c.nome, s.nome
        ");

        $stmt->execute([$salao_id]);

        return $stmt->fetchAll();
    }

    public function criar(array $dados)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO servicos (
                salao_id,
                categoria_id,
                nome,
                descricao,
                preco,
                duracao_min,
                ativo
            )
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");

        $stmt->execute([
            $dados['salao_id'],
            $dados['categoria_id'],
            $dados['nome'],
            $dados['descricao'],
            $dados['preco'],
            $dados['duracao_min']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function atualizar(array $dados)
    {
        $stmt = $this->pdo->prepare("
            UPDATE servicos
            SET
                categoria_id = ?,
                nome = ?,
                descricao = ?,
                preco = ?,
                duracao_min = ?,
                ativo = ?
            WHERE id = ?
            AND salao_id = ?
        ");

        return $stmt->execute([
            $dados['categoria_id'],
            $dados['nome'],
            $dados['descricao'],
            $dados['preco'],
            $dados['duracao_min'],
            $dados['ativo'],
            $dados['id'],
            $dados['salao_id']
        ]);
    }

    public function excluir($id, $salao_id)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM servicos
            WHERE id = ?
            AND salao_id = ?
        ");

        return $stmt->execute([
            $id,
            $salao_id
        ]);
    }
}