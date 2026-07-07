<?php

require_once '../config/headers.php';
require_once '../config/database.php';
require_once '../models/Profissional.php';

$metodo = $_SERVER['REQUEST_METHOD'];

$model = new Profissional();

// Temporário até implementarmos autenticação
$salao_id = 1;

if ($metodo === 'GET') {

    $id = $_GET['id'] ?? null;
    $servico_id = $_GET['servico_id'] ?? null;
    $apenasAtivos = !isset($_GET['todos']);

    // Buscar um profissional
    if ($id) {

        $profissional = $model->buscar(
            $salao_id,
            $id
        );

        if (!$profissional) {

            http_response_code(404);

            echo json_encode([
                'erro' => 'Profissional não encontrado.'
            ]);

            exit;
        }

        echo json_encode($profissional);

        exit;
    }

    // Listar profissionais
    echo json_encode(
        $model->listar(
            $salao_id,
            $servico_id,
            $apenasAtivos
        )
    );

} elseif ($metodo === 'POST') {

    $body = json_decode(
        file_get_contents('php://input'),
        true
    );

    $nome = trim($body['nome'] ?? '');
    $especialidade = trim($body['especialidade'] ?? '');
    $foto_url = trim($body['foto_url'] ?? '');
    $servicos = $body['servicos'] ?? [];

    if (!$nome) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'Nome é obrigatório.'
        ]);

        exit;
    }

    $id = $model->criar([
        'salao_id' => $salao_id,
        'nome' => $nome,
        'especialidade' => $especialidade,
        'foto_url' => $foto_url,
        'servicos' => $servicos
    ]);

    http_response_code(201);

    echo json_encode([
        'sucesso' => true,
        'id' => (int)$id
    ]);

} elseif ($metodo === 'PUT') {

    $body = json_decode(
        file_get_contents('php://input'),
        true
    );

    $dados = [
        'id' => $body['id'] ?? null,
        'salao_id' => $salao_id,
        'nome' => trim($body['nome'] ?? ''),
        'especialidade' => trim($body['especialidade'] ?? ''),
        'foto_url' => trim($body['foto_url'] ?? ''),
        'ativo' => isset($body['ativo']) ? (int)$body['ativo'] : 1,
        'servicos' => $body['servicos'] ?? []
    ];

    if (
        !$dados['id'] ||
        !$dados['nome']
    ) {

        http_response_code(400);

        echo json_encode([
            'erro' => 'ID e nome são obrigatórios.'
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
            'erro' => 'ID é obrigatório.'
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