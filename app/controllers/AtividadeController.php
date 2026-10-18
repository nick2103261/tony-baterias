<?php

class AtividadeController
{
    private LogAtividade $logModel;

    public function __construct()
    {
        $this->logModel = new LogAtividade();
    }

    public function listarRecentes(int $limite = 50): array
    {
        return $this->logModel->listarRecentes($limite);
    }
}