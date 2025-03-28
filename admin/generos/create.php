<?php
/**
 * Página de cadastro de gêneros
 * Permite adicionar novos gêneros ao catálogo
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../../includes/db.php';

// Verifica se o usuário é administrador
require_admin();

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $erro = false;
    
    // Verifica se o nome foi preenchido
    if (empty($nome)) {
        $_SESSION['error'] = "O nome do gênero é obrigatório.";
        $erro = true;
    }
    
    // Verifica se o gênero já existe
    if (!$erro) {
        $genero_existente = fetch_assoc("SELECT id FROM generos WHERE nome = '" . escape($nome) . "' LIMIT 1");
        
        if ($genero_existente) {
            $_SESSION['error'] = "Este gênero já está cadastrado.";
            $erro = true;
        }
    }
    
    // Insere o gênero no banco de dados
    if (!$erro) {
        $sql = "INSERT INTO generos (nome) VALUES ('" . escape($nome) . "')";
        
        if (query($sql)) {
            $_SESSION['success'] = "Gênero cadastrado com sucesso!";
            redirect(BASE_URL . '/admin/generos/generos.php');
        } else {
            $_SESSION['error'] = "Erro ao cadastrar gênero.";
        }
    }
}

// Inclui o cabeçalho
include_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Adicionar Gênero</h1>
    <a href="<?php echo BASE_URL; ?>/admin/generos/generos.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form id="genero-form" method="post" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Gênero *</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? sanitize($_POST['nome']) : ''; ?>" required>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-outline-secondary">Limpar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<?php
// Inclui o rodapé
include_once '../../includes/footer.php';
?>