<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Horario
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = conectar();
    }


    /**
     * Busca o expediente do profissional naquele dia da semana
     */
    public function buscarExpediente($profissional_id, $dia_semana)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                inicio,
                fim
            FROM horarios
            WHERE profissional_id = ?
              AND dia_semana = ?
            LIMIT 1
        ");

        $stmt->execute([
            $profissional_id,
            $dia_semana
        ]);

        return $stmt->fetch();
    }


    /**
     * Busca bloqueios do profissional em uma data
     */
    public function buscarBloqueios($profissional_id, $data)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                inicio,
                fim
            FROM bloqueios
            WHERE profissional_id = ?
              AND data = ?
        ");

        $stmt->execute([
            $profissional_id,
            $data
        ]);

        return $stmt->fetchAll();
    }


    /**
     * Busca agendamentos existentes no dia
     */
    public function buscarAgendamentos($profissional_id, $data)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                TIME(data_hora) AS inicio,
                duracao_min
            FROM agendamentos
            WHERE profissional_id = ?
              AND DATE(data_hora) = ?
              AND status NOT IN ('cancelado')
        ");

        $stmt->execute([
            $profissional_id,
            $data
        ]);

        return $stmt->fetchAll();
    }


    /**
     * CRUD - listar horários cadastrados
     */
    public function listar($profissional_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                profissional_id,
                dia_semana,
                inicio,
                fim
            FROM horarios
            WHERE profissional_id = ?
            ORDER BY dia_semana, inicio
        ");

        $stmt->execute([
            $profissional_id
        ]);

        return $stmt->fetchAll();
    }


    /**
     * CRUD - criar expediente
     */
    public function criar($dados)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO horarios
            (
                profissional_id,
                dia_semana,
                inicio,
                fim
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $dados['profissional_id'],
            $dados['dia_semana'],
            $dados['inicio'],
            $dados['fim']
        ]);

        return (int)$this->pdo->lastInsertId();
    }


    /**
     * CRUD - atualizar expediente
     */
    public function atualizar($dados)
    {
        $stmt = $this->pdo->prepare("
            UPDATE horarios
            SET
                dia_semana = ?,
                inicio = ?,
                fim = ?
            WHERE id = ?
              AND profissional_id = ?
        ");

        return $stmt->execute([
            $dados['dia_semana'],
            $dados['inicio'],
            $dados['fim'],
            $dados['id'],
            $dados['profissional_id']
        ]);
    }


    /**
     * CRUD - excluir expediente
     */
    public function excluir($id, $profissional_id)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM horarios
            WHERE id = ?
              AND profissional_id = ?
        ");

        return $stmt->execute([
            $id,
            $profissional_id
        ]);
    }
}