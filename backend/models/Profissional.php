<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Profissional
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = conectar();
    }

    public function listar($salao_id, $servico_id = null, $apenasAtivos = true)
    {
        if ($servico_id) {

            $sql = "
                SELECT
                    p.id,
                    p.nome,
                    p.especialidade,
                    p.foto_url,
                    p.ativo
                FROM profissionais p
                INNER JOIN profissional_servico ps
                    ON ps.profissional_id = p.id
                WHERE p.salao_id = ?
                  AND ps.servico_id = ?
            ";

            $params = [
                $salao_id,
                $servico_id
            ];

            if ($apenasAtivos) {
                $sql .= " AND p.ativo = 1";
            }

            $sql .= " ORDER BY p.nome";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        }

        $sql = "
            SELECT
                id,
                nome,
                especialidade,
                foto_url,
                ativo
            FROM profissionais
            WHERE salao_id = ?
        ";

        if ($apenasAtivos) {
            $sql .= " AND ativo = 1";
        }

        $sql .= " ORDER BY nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$salao_id]);

        $profissionais = $stmt->fetchAll();

        foreach ($profissionais as &$profissional) {

            $stmtServicos = $this->pdo->prepare("
                SELECT
                    s.id,
                    s.nome,
                    s.preco,
                    s.duracao_min
                FROM servicos s
                INNER JOIN profissional_servico ps
                    ON ps.servico_id = s.id
                WHERE ps.profissional_id = ?
                ORDER BY s.nome
            ");

            $stmtServicos->execute([
                $profissional['id']
            ]);

            $profissional['servicos'] = $stmtServicos->fetchAll();
        }

        return $profissionais;
    }

    public function buscar($salao_id, $id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                nome,
                especialidade,
                foto_url,
                ativo
            FROM profissionais
            WHERE id = ?
              AND salao_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $id,
            $salao_id
        ]);

        $profissional = $stmt->fetch();

        if (!$profissional) {
            return null;
        }

        $stmt = $this->pdo->prepare("
            SELECT
                s.id,
                s.nome,
                s.preco,
                s.duracao_min
            FROM servicos s
            INNER JOIN profissional_servico ps
                ON ps.servico_id = s.id
            WHERE ps.profissional_id = ?
            ORDER BY s.nome
        ");

        $stmt->execute([
            $id
        ]);

        $profissional['servicos'] = $stmt->fetchAll();

        return $profissional;
    }

    public function criar($dados)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO profissionais
            (
                salao_id,
                nome,
                especialidade,
                foto_url,
                ativo
            )
            VALUES (?, ?, ?, ?, 1)
        ");

        $stmt->execute([
            $dados['salao_id'],
            $dados['nome'],
            $dados['especialidade'],
            $dados['foto_url'] ?: null
        ]);

        $id = (int)$this->pdo->lastInsertId();

        foreach ($dados['servicos'] as $servico_id) {

            $stmt = $this->pdo->prepare("
                INSERT INTO profissional_servico
                (
                    profissional_id,
                    servico_id
                )
                VALUES (?, ?)
            ");

            $stmt->execute([
                $id,
                $servico_id
            ]);
        }

        return $id;
    }

    public function atualizar($dados)
    {
        $stmt = $this->pdo->prepare("
            UPDATE profissionais
            SET
                nome = ?,
                especialidade = ?,
                foto_url = ?,
                ativo = ?
            WHERE id = ?
              AND salao_id = ?
        ");

        $stmt->execute([
            $dados['nome'],
            $dados['especialidade'],
            $dados['foto_url'] ?: null,
            $dados['ativo'],
            $dados['id'],
            $dados['salao_id']
        ]);

        $this->pdo
            ->prepare("
                DELETE
                FROM profissional_servico
                WHERE profissional_id = ?
            ")
            ->execute([
                $dados['id']
            ]);

        foreach ($dados['servicos'] as $servico_id) {

            $this->pdo
                ->prepare("
                    INSERT INTO profissional_servico
                    (
                        profissional_id,
                        servico_id
                    )
                    VALUES (?, ?)
                ")
                ->execute([
                    $dados['id'],
                    $servico_id
                ]);
        }

        return true;
    }

    public function excluir($id, $salao_id)
    {
        $stmt = $this->pdo->prepare("
            DELETE
            FROM profissionais
            WHERE id = ?
              AND salao_id = ?
        ");

        return $stmt->execute([
            $id,
            $salao_id
        ]);
    }
}