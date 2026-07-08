<?php

require_once '../config/headers.php';
require_once '../models/Salao.php';


$method = $_SERVER['REQUEST_METHOD'];


$salao = new Salao();



switch ($method) {


    case 'GET':

        $slug = $_GET['slug'] ?? null;


        if (!$slug) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Slug é obrigatório'
            ]);

            exit;
        }



        $resultado = $salao->buscarPorSlug($slug);



        if (!$resultado) {

            http_response_code(404);

            echo json_encode([
                'erro' => 'Salão não encontrado'
            ]);

            exit;
        }



        echo json_encode($resultado);


        break;



    default:

        http_response_code(405);

        echo json_encode([
            'erro' => 'Método não permitido'
        ]);

        break;
}