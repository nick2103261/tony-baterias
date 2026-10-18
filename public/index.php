<?php
require_once __DIR__ . '/../config/config.php';

redirecionar(usuarioLogado() ? 'dashboard.php' : 'login.php');
