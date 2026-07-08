<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Agendamento
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = conectar();
    }


    public function listar($profissional_id = null, $data = null)
    {
        $sql = "
            SELECT
                a.id,
                a.data_hora,
                a.duracao_min,
                a.status,
                a.observacao,

                c.id AS cliente_id,
                c.nome AS cliente_nome,

                s.id AS servico_id,
                s.nome AS servico_nome

            FROM agendamentos a

            INNER JOIN clientes c
                ON c.id = a.cliente_id

            INNER JOIN servicos s
                ON s.id = a.servico_id

            WHERE 1 = 1
        ";

        $params = [];


        if ($profissional_id) {

            $sql .= "
                AND a.profissional_id = ?
            ";

            $params[] = $profissional_id;
        }


        if ($data) {

            $sql .= "
                AND DATE(a.data_hora) = ?
            ";

            $params[] = $data;
        }


        $sql .= "
            ORDER BY a.data_hora
        ";


        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);


        return $stmt->fetchAll();
    }



    public function buscar($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM agendamentos
            WHERE id = ?
            LIMIT 1
        ");


        $stmt->execute([
            $id
        ]);


        return $stmt->fetch();
    }



    /**
     * Verifica se o profissional atende o serviço
     */
    public function profissionalAtendeServico(
        $profissional_id,
        $servico_id
    ) {

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM profissional_servico
            WHERE profissional_id = ?
            AND servico_id = ?
        ");


        $stmt->execute([
            $profissional_id,
            $servico_id
        ]);


        return $stmt->fetchColumn() > 0;
    }



    /**
     * Busca duração oficial do serviço
     */
    public function buscarDuracaoServico($servico_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT duracao_min
            FROM servicos
            WHERE id = ?
            LIMIT 1
        ");


        $stmt->execute([
            $servico_id
        ]);


        return $stmt->fetchColumn();
    }



    public function validarDisponibilidade(
        $profissional_id,
        $data_hora,
        $duracao_min
    ) {

        $inicio = strtotime($data_hora);

        $fim = $inicio + ($duracao_min * 60);



        $stmt = $this->pdo->prepare("
            SELECT
                data_hora,
                duracao_min
            FROM agendamentos
            WHERE profissional_id = ?
            AND DATE(data_hora) = DATE(?)
            AND status NOT IN ('cancelado')
        ");


        $stmt->execute([
            $profissional_id,
            $data_hora
        ]);


        $agendamentos = $stmt->fetchAll();



        foreach ($agendamentos as $agendamento) {

            $inicioExistente =
                strtotime($agendamento['data_hora']);


            $fimExistente =
                $inicioExistente +
                ($agendamento['duracao_min'] * 60);



            if (
                $inicio < $fimExistente &&
                $fim > $inicioExistente
            ) {

                return false;
            }
        }


        return true;
    }



    public function criar($dados)
    {

        /**
         * Valida se profissional executa o serviço
         */
        if (
            !$this->profissionalAtendeServico(
                $dados['profissional_id'],
                $dados['servico_id']
            )
        ) {

            return false;
        }



        /**
         * Busca duração oficial do serviço
         */
        $duracao = $this->buscarDuracaoServico(
            $dados['servico_id']
        );


        if (!$duracao) {

            return false;
        }



        /**
         * Valida conflito de horário
         */
        if (
            !$this->validarDisponibilidade(
                $dados['profissional_id'],
                $dados['data_hora'],
                $duracao
            )
        ) {

            return false;
        }



        $stmt = $this->pdo->prepare("
            INSERT INTO agendamentos
            (
                cliente_id,
                profissional_id,
                servico_id,
                data_hora,
                duracao_min,
                status,
                observacao
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");



        $stmt->execute([

            $dados['cliente_id'],

            $dados['profissional_id'],

            $dados['servico_id'],

            $dados['data_hora'],

            $duracao,

            $dados['status'] ?? 'pendente',

            $dados['observacao'] ?? null

        ]);



        return (int)$this->pdo->lastInsertId();
    }



    public function cancelar($id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE agendamentos
            SET status = 'cancelado'
            WHERE id = ?
        ");


        return $stmt->execute([
            $id
        ]);
    }
}