<?php

require_once '../config/headers.php';
require_once '../config/database.php';
require_once '../models/Cliente.php';

$metodo = $_SERVER['REQUEST_METHOD'];

$model = new Cliente();

// Temporário até implementarmos autenticação
$salao_id = 1;

if ($metodo === 'POST') {

    $body = json_decode(
        file_get_contents('php://input'),
        true
    );

    $nome = trim($body['nome'] ?? '');
    $telefone = trim($body['telefone'] ?? '');
    $email = trim($body['email'] ?? '');

    if (!$nome || !$telefone) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'Nome e telefone são obrigatórios.'
        ]);

        exit;
    }

    echo json_encode(
        $model->criar(
            $salao_id,
            $nome,
            $telefone,
            $email
        )
    );
} elseif ($metodo === 'GET') {

    $id = $_GET['id'] ?? null;
    $email = $_GET['email'] ?? null;


    // Lista todos os clientes do salão
    if (!$id && !$email) {

        echo json_encode(
            $model->listar($salao_id)
        );

        exit;
    }


    $cliente = $model->buscar(
        $salao_id,
        $id,
        $email
    );


    if (!$cliente) {

        http_response_code(404);

        echo json_encode([
            'erro' => 'Cliente não encontrado.'
        ]);

        exit;
    }


    echo json_encode($cliente);

} else {

    http_response_code(405);

    echo json_encode([
        'erro' => 'Método não permitido.'
    ]);
}