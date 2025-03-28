<?php
/**
 * Página de edição de gêneros
 * Permite atualizar gêneros existentes no catálogo
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../../includes/db.php';

// Verifica se o usuário é administrador
require_admin();

// Verifica se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID do gênero não fornecido.";
    redirect(BASE_URL . '/admin/generos/generos.php');
}

$genero_id = (int)$_GET['id'];

// Busca o gênero pelo ID
$genero = fetch_one("SELECT * FROM generos WHERE id = $genero_id");

// Verifica se o gênero existe
if (!$genero) {
    $_SESSION['error'] = "Gênero não encontrado.";
    redirect(BASE_URL . '/admin/generos/generos.php');
}

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $erro = false;
    
    // Verifica se o nome foi preenchido
    if (empty($nome)) {
        $_SESSION['error'] = "O nome do gênero é obrigatório.";
        $erro = true;
    }
    
    // Verifica se o gênero já existe com este nome (exceto o atual)
    if (!$erro) {
        $genero_existente = fetch_one("SELECT id FROM generos WHERE nome = '" . escape($nome) . "' AND id != $genero_id LIMIT 1");
        
        if ($genero_existente) {
            $_SESSION['error'] = "Já existe outro gênero com este nome.";
            $erro = true;
        }
    }
    
    // Atualiza o gênero no banco de dados
    if (!$erro) {
        $sql = "UPDATE generos SET nome = '" . escape($nome) . "' WHERE id = $genero_id";
        
        if (query($sql)) {
            $_SESSION['success'] = "Gênero atualizado com sucesso!";
            redirect(BASE_URL . '/admin/generos/generos.php');
        } else {
            $_SESSION['error'] = "Erro ao atualizar gênero.";
        }
    }
}

// Inclui o cabeçalho
include_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Editar Gênero</h1>
    <a href="<?php echo BASE_URL; ?>/admin/generos/generos.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form id="genero-form" method="post" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Gênero *</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? sanitize($_POST['nome']) : sanitize($genero['nome']); ?>" required>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo BASE_URL; ?>/admin/generos/generos.php" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<?php
// Inclui o rodapé
include_once '../../includes/footer.php';
?>