<?php

class PessoasController
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
            "SELECT * FROM pessoas ORDER BY nome ASC"
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
            "SELECT * FROM pessoas WHERE id = :id"
        );
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        echo json_encode(
            $stmt->fetch(PDO::FETCH_ASSOC),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function criar(): void
    {
        header('Content-Type: application/json');

        $nome       = trim($_POST['nome']       ?? '');
        $email      = trim($_POST['email']      ?? '');
        $telefone   = trim($_POST['telefone']   ?? '');
        $documento  = trim($_POST['documento']  ?? '');
        $curso      = trim($_POST['curso']      ?? '');
        $periodo    = trim($_POST['periodo']    ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status     = $_POST['status']          ?? 'ativo';

        $sql = "INSERT INTO pessoas
                (nome, email, telefone, status, documento, curso, periodo, observacoes)
                VALUES
                (:nome, :email, :telefone, :status, :documento, :curso, :periodo, :observacoes)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome'        => $nome,
            ':email'       => $email,
            ':telefone'    => $telefone,
            ':status'      => $status,
            ':documento'   => $documento,
            ':curso'       => $curso,
            ':periodo'     => $periodo,
            ':observacoes' => $observacoes,
        ]);

        echo json_encode(['mensagem' => 'Pessoa cadastrada com sucesso.']);
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;

        $sql = "UPDATE pessoas
                SET nome        = :nome,
                    email       = :email,
                    telefone    = :telefone,
                    status      = :status,
                    documento   = :documento,
                    curso       = :curso,
                    periodo     = :periodo,
                    observacoes = :observacoes
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'          => $id,
            ':nome'        => $_POST['nome']        ?? '',
            ':email'       => $_POST['email']       ?? '',
            ':telefone'    => $_POST['telefone']    ?? '',
            ':status'      => $_POST['status']      ?? 'ativo',
            ':documento'   => $_POST['documento']   ?? '',
            ':curso'       => $_POST['curso']       ?? '',
            ':periodo'     => $_POST['periodo']     ?? null,
            ':observacoes' => $_POST['observacoes'] ?? null,
        ]);

        echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso.']);
    }

    public function inativar(): void
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;

        $stmt = $this->pdo->prepare(
            "UPDATE pessoas SET status = 'inativo' WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        echo json_encode(['mensagem' => 'Pessoa inativada com sucesso.']);
    }
}
