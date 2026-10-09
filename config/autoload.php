<?php

spl_autoload_register(function (string $class) {
    $pastas = [
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/controllers/',
    ];

    foreach ($pastas as $pasta) {
        $arquivo = $pasta . $class . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});
