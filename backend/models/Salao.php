<?php

require_once '../config/headers.php';
require_once __DIR__ . '/../config/database.php';

class Salao
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = conectar();
    }

    // Busca salão pelo slug
    public function buscarPorSlug($slug)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                nome,
                slug,
                logo_url,
                telefone,
                email,
                endereco,
                ativo
            FROM saloes
            WHERE slug = ?
            AND ativo = 1
            LIMIT 1
        ");


        $stmt->execute([
            $slug
        ]);


        return $stmt->fetch();
    }

    // Busca salão pelo ID
    public function buscar($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                nome,
                slug,
                logo_url,
                telefone,
                email,
                endereco,
                ativo
            FROM saloes
            WHERE id = ?
            LIMIT 1
        ");


        $stmt->execute([
            $id
        ]);


        return $stmt->fetch();
    }

    // Lista todos os salões ativos
    public function listar()
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                nome,
                slug,
                logo_url,
                telefone,
                email,
                endereco,
                ativo
            FROM saloes
            WHERE ativo = 1
            ORDER BY nome
        ");


        $stmt->execute();


        return $stmt->fetchAll();
    }
}