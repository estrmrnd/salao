<?php

require_once '../config/headers.php';

define('DB_HOST', 'db');
define('DB_NAME', 'salao_db');
define('DB_USER', 'salao');
define('DB_PASS', '1234');

function conectar() {

    try {

        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        return $pdo;

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            'erro' => 'Falha na conexão com o banco',
            'detalhe' => $e->getMessage()
        ]);

        exit;
    }
}