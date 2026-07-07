<?php

require_once '../config/headers.php';
require_once '../config/database.php';
require_once '../models/Servico.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$model = new Servico();

// Temporário até implementarmos autenticação
$salao_id = 1;

if ($metodo === 'GET') {

    echo json_encode(
        $model->listar($salao_id)
    );

} elseif ($metodo === 'POST') {

    $body = json_decode(file_get_contents('php://input'), true);

    $nome         = trim($body['nome'] ?? '');
    $descricao    = trim($body['descricao'] ?? '');
    $preco        = $body['preco'] ?? null;
    $duracao_min  = $body['duracao_min'] ?? null;
    $categoria_id = $body['categoria_id'] ?? null;

    if (!$nome || !$preco || !$duracao_min || !$categoria_id) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'Nome, preço, duração e categoria são obrigatórios.'
        ]);

        exit;
    }

    $id = $model->criar([
        'salao_id'    => $salao_id,
        'categoria_id'=> $categoria_id,
        'nome'        => $nome,
        'descricao'   => $descricao,
        'preco'       => $preco,
        'duracao_min' => $duracao_min
    ]);

    http_response_code(201);

    echo json_encode([
        'sucesso' => true,
        'id' => (int)$id
    ]);

} elseif ($metodo === 'PUT') {

    $body = json_decode(file_get_contents('php://input'), true);

    $dados = [
        'id'           => $body['id'] ?? null,
        'salao_id'     => $salao_id,
        'nome'         => trim($body['nome'] ?? ''),
        'descricao'    => trim($body['descricao'] ?? ''),
        'preco'        => $body['preco'] ?? null,
        'duracao_min'  => $body['duracao_min'] ?? null,
        'categoria_id' => $body['categoria_id'] ?? null,
        'ativo'        => isset($body['ativo']) ? (int)$body['ativo'] : 1
    ];

    if (
        !$dados['id'] ||
        !$dados['nome'] ||
        !$dados['preco'] ||
        !$dados['duracao_min'] ||
        !$dados['categoria_id']
    ) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'ID, nome, preço, duração e categoria são obrigatórios.'
        ]);

        exit;
    }

    $model->atualizar($dados);

    echo json_encode([
        'sucesso' => true
    ]);

} elseif ($metodo === 'DELETE') {

    $id = $_GET['id'] ?? null;

    if (!$id) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'ID obrigatório.'
        ]);

        exit;
    }

    $model->excluir(
        $id,
        $salao_id
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