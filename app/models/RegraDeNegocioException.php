<?php

// Erro previsto, com mensagem pronta para mostrar ao usuário (diferente de erro de banco, que nunca vai para a tela)
class RegraDeNegocioException extends RuntimeException
{
}
