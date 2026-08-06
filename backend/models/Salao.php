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

    $salao = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$salao) {
        return false;
    }


    // Busca categorias e serviços
    $stmt = $this->pdo->prepare("
        SELECT
            c.id AS categoria_id,
            c.nome AS categoria_nome,

            s.id AS servico_id,
            s.nome AS servico_nome,
            s.descricao,
            s.preco,
            s.duracao_min,
            s.ativo

        FROM categorias c

        LEFT JOIN servicos s
            ON s.categoria_id = c.id

        WHERE c.salao_id = ?

        ORDER BY c.id, s.id
    ");


    $stmt->execute([
        $salao['id']
    ]);


    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $categorias = [];


    foreach ($dados as $item) {

        $categoriaId = $item['categoria_id'];


        if (!isset($categorias[$categoriaId])) {

            $categorias[$categoriaId] = [
                "id" => $categoriaId,
                "nome" => $item['categoria_nome'],
                "servicos" => []
            ];

        }


        if ($item['servico_id']) {

            $categorias[$categoriaId]["servicos"][] = [
                "id" => $item['servico_id'],
                "nome" => $item['servico_nome'],
                "descricao" => $item['descricao'],
                "preco" => $item['preco'],
                "duracao_min" => $item['duracao_min']
            ];

        }

    }


    $salao["categorias"] = array_values($categorias);


    return $salao;
}
}