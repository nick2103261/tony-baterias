<?php

class CategoriaController
{
    private Categoria $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
    }

    public function listar(): array
    {
        return $this->categoriaModel->listarTodas();
    }

    public function criar(string $nome): array
    {
        $nome = trim($nome);

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'Informe o nome da categoria.'];
        }

        try {
            $this->categoriaModel->criar($nome);
            return ['sucesso' => true, 'mensagem' => 'Categoria cadastrada com sucesso.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['sucesso' => false, 'mensagem' => 'Já existe uma categoria com esse nome.'];
            }
            throw $e;
        }
    }

    public function excluir(int $id): void
    {
        $this->categoriaModel->excluir($id);
    }
}
