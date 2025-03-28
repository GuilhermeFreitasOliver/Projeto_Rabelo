<?php
/**
 * Página de logout
 * Encerra a sessão do usuário e redireciona para a página inicial
 */

// Inclui o arquivo de configuração
require_once '../includes/config.php';

// Destrói a sessão
session_destroy();

// Redireciona para a página inicial com mensagem de sucesso
$_SESSION['success'] = "Logout realizado com sucesso!";
redirect(BASE_URL);
?>