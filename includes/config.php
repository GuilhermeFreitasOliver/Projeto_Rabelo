<?php
/**
 * Arquivo de configuração do sistema
 * Contém constantes e configurações globais
 */

// Configurações de ambiente
define('DEBUG', true); // Altere para false em produção

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Altere conforme suas credenciais
define('DB_PASS', ''); // Altere conforme suas credenciais
define('DB_NAME', 'catalogo_filmes');

// Configurações de URL e caminhos
define('BASE_URL', 'http://localhost/Projeto%20Rabelo'); // Ajuste conforme seu ambiente
define('UPLOAD_DIR', __DIR__ . '/../assets/images/uploads/');

// Configurações de sessão
session_start();

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de upload de imagens
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('THUMB_WIDTH', 300);
define('THUMB_HEIGHT', 450);

// Função para exibir mensagens de erro em modo de desenvolvimento
function debug($data) {
    if (DEBUG) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

// Função para redirecionar
function redirect($url) {
    header("Location: $url");
    exit;
}

// Função para verificar se o usuário está logado
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Função para verificar se o usuário é administrador
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Função para proteger páginas administrativas
function require_admin() {
    if (!is_logged_in() || !is_admin()) {
        $_SESSION['error'] = "Acesso restrito. Faça login como administrador.";
        redirect(BASE_URL . '/auth/login.php');
    }
}

// Função para sanitizar inputs
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>