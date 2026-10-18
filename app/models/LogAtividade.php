<?php

class LogAtividade
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function registrar(int $usuarioId, string $acao, string $tipoRegistro, ?int $registroId = null): void
    {
        $sql = 'INSERT INTO log_atividades (usuario_id, acao, tipo_registro, registro_id)
                VALUES (:usuario_id, :acao, :tipo_registro, :registro_id)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id'    => $usuarioId,
            ':acao'          => $acao,
            ':tipo_registro' => $tipoRegistro,
            ':registro_id'   => $registroId,
        ]);
    }

    public function listarRecentes(int $limite = 50): array
    {
        $sql = "SELECT l.*, u.nome AS usuario_nome
                FROM log_atividades l
                JOIN usuarios u ON u.id = l.usuario_id
                ORDER BY l.criado_em DESC, l.id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}