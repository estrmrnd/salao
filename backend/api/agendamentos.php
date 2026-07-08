<?php

require_once '../config/headers.php';
require_once '../models/Agendamento.php';


$method = $_SERVER['REQUEST_METHOD'];


$agendamento = new Agendamento();



switch ($method) {


    case 'GET':

        $profissional_id = $_GET['profissional_id'] ?? null;
        $data = $_GET['data'] ?? null;
        $id = $_GET['id'] ?? null;


        if ($id) {

            $resultado = $agendamento->buscar($id);

        } else {

            $resultado = $agendamento->listar(
                $profissional_id,
                $data
            );

        }


        echo json_encode($resultado);

        break;



    case 'POST':

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );


        if (
            !$dados ||
            !isset($dados['cliente_id']) ||
            !isset($dados['profissional_id']) ||
            !isset($dados['servico_id']) ||
            !isset($dados['data_hora']) ||
            !isset($dados['duracao_min'])
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Dados obrigatórios não informados'
            ]);

            exit;
        }



        $id = $agendamento->criar($dados);



        if (!$id) {

            http_response_code(409);

            echo json_encode([
                'erro' => 'Horário indisponível'
            ]);

            exit;
        }



        echo json_encode([
            'id' => $id,
            'mensagem' => 'Agendamento criado com sucesso'
        ]);


        break;




    case 'PUT':

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );


        if (
            !isset($dados['id'])
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Informe o id do agendamento'
            ]);

            exit;
        }


        // por enquanto apenas cancelamento
        $resultado = $agendamento->cancelar(
            $dados['id']
        );


        echo json_encode([
            'sucesso' => $resultado
        ]);


        break;



    default:

        http_response_code(405);

        echo json_encode([
            'erro' => 'Método não permitido'
        ]);

        break;
}