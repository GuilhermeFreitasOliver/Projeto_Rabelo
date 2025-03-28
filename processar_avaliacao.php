<?php
/**
 * Processamento de avaliações
 * Salva ou atualiza avaliações de filmes
 */

// Inclui o arquivo de conexão com o banco de dados
require_once 'includes/db.php';

// Verifica se o usuário está logado
if (!is_logged_in()) {
    $_SESSION['error'] = "Você precisa estar logado para avaliar filmes.";
    redirect(BASE_URL);
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL);
}

// Obtém os dados do formulário
$filme_id = isset($_POST['filme_id']) ? (int) $_POST['filme_id'] : 0;
$avaliacao_id = isset($_POST['avaliacao_id']) ? (int) $_POST['avaliacao_id'] : 0;
$estrelas = isset($_POST['estrelas']) ? (int) $_POST['estrelas'] : 0;
$comentario = isset($_POST['comentario']) ? sanitize($_POST['comentario']) : '';
$usuario_id = $_SESSION['user_id'];

// Valida os dados
if ($filme_id <= 0) {
    $_SESSION['error'] = "ID do filme inválido.";
    redirect(BASE_URL);
}

// Verifica se o filme existe
$filme = fetch_assoc("SELECT id FROM filmes WHERE id = $filme_id");
if (!$filme) {
    $_SESSION['error'] = "Filme não encontrado.";
    redirect(BASE_URL);
}

// Valida a classificação por estrelas
if ($estrelas < 1 || $estrelas > 5) {
    $_SESSION['error'] = "Classificação inválida. Escolha de 1 a 5 estrelas.";
    redirect(BASE_URL . "/filme.php?id=$filme_id");
}

// Verifica se é uma atualização ou uma nova avaliação
if ($avaliacao_id > 0) {
    // Verifica se a avaliação pertence ao usuário
    $avaliacao = fetch_assoc("SELECT id FROM avaliacoes WHERE id = $avaliacao_id AND usuario_id = $usuario_id");
    
    if (!$avaliacao) {
        $_SESSION['error'] = "Você não tem permissão para editar esta avaliação.";
        redirect(BASE_URL . "/filme.php?id=$filme_id");
    }
    
    // Atualiza a avaliação existente
    $sql = "UPDATE avaliacoes SET 
            estrelas = $estrelas, 
            comentario = '" . escape($comentario) . "', 
            data_avaliacao = NOW() 
            WHERE id = $avaliacao_id";
    
    if (query($sql)) {
        $_SESSION['success'] = "Sua avaliação foi atualizada com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao atualizar sua avaliação. Tente novamente.";
    }
} else {
    // Verifica se o usuário já avaliou este filme
    $avaliacao_existente = fetch_assoc("SELECT id FROM avaliacoes WHERE filme_id = $filme_id AND usuario_id = $usuario_id");
    
    if ($avaliacao_existente) {
        // Atualiza a avaliação existente
        $sql = "UPDATE avaliacoes SET 
                estrelas = $estrelas, 
                comentario = '" . escape($comentario) . "', 
                data_avaliacao = NOW() 
                WHERE id = " . $avaliacao_existente['id'];
    } else {
        // Insere uma nova avaliação
        $sql = "INSERT INTO avaliacoes (filme_id, usuario_id, estrelas, comentario, data_avaliacao) 
                VALUES ($filme_id, $usuario_id, $estrelas, '" . escape($comentario) . "', NOW())";
    }
    
    if (query($sql)) {
        $_SESSION['success'] = "Sua avaliação foi enviada com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao enviar sua avaliação. Tente novamente.";
    }
}

// Redireciona de volta para a página do filme
redirect(BASE_URL . "/filme.php?id=$filme_id");