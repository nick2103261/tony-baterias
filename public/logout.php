<?php
require_once __DIR__ . '/../config/bootstrap.php';

$controller = new AuthController();
$controller->logout();
