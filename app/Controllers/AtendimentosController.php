<?php

class AtendimentosController
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

        $sql = "
        SELECT
            a.id,
            p.nome AS pessoa,
            t.nome AS tipo,
            u.nome AS responsavel,
            a.descricao,
            a.status,
            a.data_atendimento,
            a.horario_atendimento,
            a.observacao_final
        FROM atendimentos a
        INNER JOIN pessoas p          ON a.pessoa_id           = p.id
        INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
        INNER JOIN usuarios u          ON a.usuario_id          = u.id
        ORDER BY a.id DESC
        ";

        $stmt = $this->pdo->query($sql);

        echo json_encode(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function visualizar(): void
    {
        header('Content-Type: application/json');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $stmt = $this->pdo->prepare(
            "SELECT * FROM atendimentos WHERE id = :id"
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

        // Responsável sempre vem da sessão, nunca do formulário
        $usuario_id = isset($_SESSION['usuario']['id'])
            ? (int) $_SESSION['usuario']['id']
            : null;

        if (!$usuario_id) {
            http_response_code(401);
            echo json_encode(['erro' => 'Usuário não autenticado.']);
            return;
        }

        $sql = "
        INSERT INTO atendimentos
            (pessoa_id, tipo_atendimento_id, usuario_id, descricao,
             status, observacao_final, data_atendimento, horario_atendimento)
        VALUES
            (:pessoa_id, :tipo_id, :usuario_id, :descricao,
             :status, :observacao_final, :data_atendimento, :horario_atendimento)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pessoa_id'           => $_POST['pessoa_id']          ?? null,
            ':tipo_id'             => $_POST['tipo_atendimento_id'] ?? null,
            ':usuario_id'          => $usuario_id,
            ':descricao'           => $_POST['descricao']           ?? '',
            ':status'              => $_POST['status']              ?? 'aberto',
            ':observacao_final'    => $_POST['observacao_final']    ?? null,
            ':data_atendimento'    => $_POST['data_atendimento']    ?? null,
            ':horario_atendimento' => $_POST['horario_atendimento'] ?? null,
        ]);

        echo json_encode(['mensagem' => 'Atendimento criado com sucesso.']);
    }

    public function atualizarStatus(): void
    {
        header('Content-Type: application/json');

        $stmt = $this->pdo->prepare(
            "UPDATE atendimentos
             SET status            = :status,
                 observacao_final  = :obs
             WHERE id = :id"
        );

        $stmt->execute([
            ':id'     => $_POST['id']              ?? null,
            ':status' => $_POST['status']           ?? 'aberto',
            ':obs'    => $_POST['observacao_final'] ?? null,
        ]);

        echo json_encode(['mensagem' => 'Status atualizado com sucesso.']);
    }
}
