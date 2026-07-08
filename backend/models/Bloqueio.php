<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Bloqueio
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = conectar();
    }

    // Lista bloqueios com base no ID do profissional e na data
    public function listar($profissional_id = null, $data = null)
    {
        $sql = "
            SELECT
                b.id,
                b.profissional_id,
                b.data,
                b.inicio,
                b.fim,
                b.motivo,

                p.nome AS profissional_nome

            FROM bloqueios b

            INNER JOIN profissionais p
                ON p.id = b.profissional_id

            WHERE 1 = 1
        ";


        $params = [];



        if ($profissional_id) {

            $sql .= "
                AND b.profissional_id = ?
            ";

            $params[] = $profissional_id;
        }



        if ($data) {

            $sql .= "
                AND b.data = ?
            ";

            $params[] = $data;
        }



        $sql .= "
            ORDER BY b.data, b.inicio
        ";



        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);


        return $stmt->fetchAll();
    }

    // Busca um bloqueio específico com base no ID
    public function buscar($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                profissional_id,
                data,
                inicio,
                fim,
                motivo
            FROM bloqueios
            WHERE id = ?
            LIMIT 1
        ");


        $stmt->execute([
            $id
        ]);


        return $stmt->fetch();
    }

    // Cria um novo bloqueio com base nos dados fornecidos
    public function criar($dados)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO bloqueios
            (
                profissional_id,
                data,
                inicio,
                fim,
                motivo
            )
            VALUES (?, ?, ?, ?, ?)
        ");


        $stmt->execute([

            $dados['profissional_id'],

            $dados['data'],

            $dados['inicio'],

            $dados['fim'],

            $dados['motivo'] ?? null

        ]);


        return (int)$this->pdo->lastInsertId();
    }

    // Atualizar bloqueio com base no ID e no ID do profissional
    public function atualizar($dados)
    {
        $stmt = $this->pdo->prepare("
            UPDATE bloqueios
            SET
                data = ?,
                inicio = ?,
                fim = ?,
                motivo = ?
            WHERE id = ?
              AND profissional_id = ?
        ");



        return $stmt->execute([

            $dados['data'],

            $dados['inicio'],

            $dados['fim'],

            $dados['motivo'] ?? null,

            $dados['id'],

            $dados['profissional_id']

        ]);
    }

    // Remover bloqueio com base no ID e no ID do profissional
    public function excluir($id, $profissional_id)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM bloqueios
            WHERE id = ?
              AND profissional_id = ?
        ");


        return $stmt->execute([

            $id,

            $profissional_id

        ]);
    }
}