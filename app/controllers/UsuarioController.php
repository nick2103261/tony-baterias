<?php

class UsuarioController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();

    }

    public function listar(): array
    {
        return $this->usuarioModel->listarTodos();
    }

    public function buscar(int $id): ?array
    {
        return $this->usuarioModel->buscarPorId($id);
    }

}