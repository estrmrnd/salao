<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Auth
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = conectar();
    }

    // Busca admin pelo email
    public function buscarPorEmail($email)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                salao_id,
                nome,
                email,
                senha_hash,
                papel,
                ativo
            FROM admins
            WHERE email = ?
            LIMIT 1
        ");


        $stmt->execute([
            $email
        ]);


        return $stmt->fetch();
    }

    // Realiza login
    public function login($email, $senha)
    {
        $admin = $this->buscarPorEmail($email);



        if (!$admin) {
            return false;
        }



        if (!$admin['ativo']) {
            return false;
        }



        if (!password_verify(
            $senha,
            $admin['senha_hash']
        )) {

            return false;
        }



        return [
            'id' => $admin['id'],
            'nome' => $admin['nome'],
            'email' => $admin['email'],
            'salao_id' => $admin['salao_id'],
            'papel' => $admin['papel']
        ];
    }
}