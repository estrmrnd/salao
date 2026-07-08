<?php

require_once '../config/headers.php';
require_once '../models/Bloqueio.php';


$method = $_SERVER['REQUEST_METHOD'];


$bloqueio = new Bloqueio();



switch ($method) {


    case 'GET':

        $id = $_GET['id'] ?? null;

        $profissional_id = $_GET['profissional_id'] ?? null;

        $data = $_GET['data'] ?? null;



        if ($id) {

            $resultado = $bloqueio->buscar($id);

        } else {

            $resultado = $bloqueio->listar(
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
            !isset($dados['profissional_id']) ||
            !isset($dados['data']) ||
            !isset($dados['inicio']) ||
            !isset($dados['fim'])
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Dados obrigatórios não informados'
            ]);

            exit;
        }




        $id = $bloqueio->criar($dados);



        echo json_encode([

            'id' => $id,

            'mensagem' => 'Bloqueio criado com sucesso'

        ]);



        break;




    case 'PUT':

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );



        if (
            !$dados ||
            !isset($dados['id']) ||
            !isset($dados['profissional_id'])
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Informe id e profissional_id'
            ]);

            exit;
        }




        $resultado = $bloqueio->atualizar($dados);



        echo json_encode([

            'sucesso' => $resultado

        ]);



        break;





    case 'DELETE':

        $id = $_GET['id'] ?? null;

        $profissional_id = $_GET['profissional_id'] ?? null;



        if (
            !$id ||
            !$profissional_id
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Informe id e profissional_id'
            ]);

            exit;
        }




        $resultado = $bloqueio->excluir(
            $id,
            $profissional_id
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