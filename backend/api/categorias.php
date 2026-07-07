<?php

require_once '../config/headers.php';
require_once '../config/database.php';
require_once '../models/Categoria.php';

$metodo = $_SERVER['REQUEST_METHOD'];

$model = new Categoria();

// Temporário até implementarmos autenticação
$salao_id = 1;

if ($metodo === 'GET') {

    echo json_encode(
        $model->listar($salao_id)
    );

} elseif ($metodo === 'POST') {

    $body = json_decode(
        file_get_contents('php://input'),
        true
    );

    $nome = trim($body['nome'] ?? '');

    if (!$nome) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'Nome da categoria é obrigatório.'
        ]);

        exit;
    }

    $model->criar(
        $salao_id,
        $nome
    );

    echo json_encode([
        'sucesso' => true
    ]);

} elseif ($metodo === 'PUT') {

    $body = json_decode(
        file_get_contents('php://input'),
        true
    );

    $id = $body['id'] ?? null;
    $nome = trim($body['nome'] ?? '');

    if (!$id || !$nome) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'ID e nome são obrigatórios.'
        ]);

        exit;
    }

    $model->atualizar(
        $salao_id,
        $id,
        $nome
    );

    echo json_encode([
        'sucesso' => true
    ]);

} elseif ($metodo === 'DELETE') {

    $id = $_GET['id'] ?? null;

    if (!$id) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'ID da categoria é obrigatório.'
        ]);

        exit;
    }

    $model->excluir(
        $salao_id,
        $id
    );

    echo json_encode([
        'sucesso' => true
    ]);

} else {

    http_response_code(405);

    echo json_encode([
        'erro' => 'Método não permitido.'
    ]);

}