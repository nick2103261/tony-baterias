<?php

class MovimentacaoEstoque
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }
    
    public function registrar(int $produtoId, int $usuarioId, string $tipo, int $quantidade, ?int $fornecedorId, ?string $observacao = null): void
    {
        if (!in_array($tipo, ['entrada', 'saida'], true)) {
            throw new InvalidArgumentException('Tipo de movimentação inválido.');
        }

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO movimentacoes_estoque (produto_id, usuario_id, fornecedor_id, tipo, quantidade, observacao)
                 VALUES (:produto_id, :usuario_id, :fornecedor_id, :tipo, :quantidade, :observacao)'
            );
            $stmt->execute([
                ':produto_id'    => $produtoId,
                ':usuario_id'    => $usuarioId,
                ':fornecedor_id' => $tipo === 'entrada' ? $fornecedorId : null,
                ':tipo'          => $tipo,
                ':quantidade'    => $quantidade,
                ':observacao'    => $observacao,
            ]);

            $operador = $tipo === 'entrada' ? '+' : '-';
            $sqlEstoque = "UPDATE produtos SET estoque_atual = estoque_atual {$operador} :quantidade WHERE id = :produto_id";
            $stmtEstoque = $this->db->prepare($sqlEstoque);
            $stmtEstoque->execute([
                ':quantidade' => $quantidade,
                ':produto_id' => $produtoId,
            ]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function listarRecentes(int $limite = 20): array
    {
        $sql = "SELECT m.*, p.nome AS produto_nome, u.nome AS usuario_nome, f.nome AS fornecedor_nome
                FROM movimentacoes_estoque m
                JOIN produtos p ON p.id = m.produto_id
                JOIN usuarios u ON u.id = m.usuario_id
                LEFT JOIN fornecedores f ON f.id = m.fornecedor_id
                ORDER BY m.criado_em DESC, m.id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
