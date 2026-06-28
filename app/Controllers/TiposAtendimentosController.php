<?php

class TiposAtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json');

        $stmt = $this->pdo->query(
            "SELECT * FROM tipos_atendimentos ORDER BY nome ASC"
        );

        echo json_encode(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $stmt = $this->pdo->prepare(
            "SELECT * FROM tipos_atendimentos WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        echo json_encode(
            $stmt->fetch(PDO::FETCH_ASSOC),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function criar(): void
    {
        header('Content-Type: application/json');

        $stmt = $this->pdo->prepare(
            "INSERT INTO tipos_atendimentos (nome, descricao, status)
             VALUES (:nome, :descricao, :status)"
        );

        $stmt->execute([
            ':nome'      => $_POST['nome']     ?? '',
            ':descricao' => $_POST['descricao'] ?? '',
            ':status'    => $_POST['status']    ?? 'ativo',
        ]);

        echo json_encode(['mensagem' => 'Tipo criado com sucesso.']);
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json');

        $stmt = $this->pdo->prepare(
            "UPDATE tipos_atendimentos
             SET nome      = :nome,
                 descricao = :descricao,
                 status    = :status
             WHERE id = :id"
        );

        $stmt->execute([
            ':id'        => $_POST['id']       ?? null,
            ':nome'      => $_POST['nome']     ?? '',
            ':descricao' => $_POST['descricao'] ?? '',
            ':status'    => $_POST['status']    ?? 'ativo',
        ]);

        echo json_encode(['mensagem' => 'Tipo atualizado com sucesso.']);
    }

    public function inativar(): void
    {
        header('Content-Type: application/json');

        $stmt = $this->pdo->prepare(
            "UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = :id"
        );
        $stmt->execute([':id' => $_POST['id'] ?? null]);

        echo json_encode(['mensagem' => 'Tipo inativado com sucesso.']);
    }
}
