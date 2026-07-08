<?php

require_once '../config/headers.php';
require_once '../models/Auth.php';



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'erro' => 'Método não permitido'
    ]);

    exit;
}



$dados = json_decode(
    file_get_contents("php://input"),
    true
);



if (
    !$dados ||
    !isset($dados['email']) ||
    !isset($dados['senha'])
) {

    http_response_code(400);

    echo json_encode([
        'erro' => 'Email e senha são obrigatórios'
    ]);

    exit;
}



$auth = new Auth();



$resultado = $auth->login(
    $dados['email'],
    $dados['senha']
);



if (!$resultado) {

    http_response_code(401);

    echo json_encode([
        'erro' => 'Credenciais inválidas'
    ]);

    exit;
}



echo json_encode($resultado);