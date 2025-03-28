<?php
/**
 * Página de cadastro
 * Permite que novos usuários se cadastrem no sistema
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../includes/db.php';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $erro = false;
    
    // Verifica se os campos foram preenchidos
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $_SESSION['error'] = "Preencha todos os campos.";
        $erro = true;
    }
    
    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        $_SESSION['error'] = "As senhas não coincidem.";
        $erro = true;
    }
    
    // Verifica se o email já está em uso
    if (!$erro) {
        $usuario_existente = fetch_assoc("SELECT id FROM usuarios WHERE email = '" . escape($email) . "' LIMIT 1");
        
        if ($usuario_existente) {
            $_SESSION['error'] = "Este email já está em uso.";
            $erro = true;
        }
    }
    
    // Cadastra o usuário se não houver erros
    if (!$erro) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES 
                ('" . escape($nome) . "', '" . escape($email) . "', '" . $senha_hash . "')";
        
        if (query($sql)) {
            $_SESSION['success'] = "Cadastro realizado com sucesso! Faça login para continuar.";
            redirect(BASE_URL . '/auth/login.php');
        } else {
            $_SESSION['error'] = "Erro ao cadastrar usuário. Tente novamente.";
        }
    }
}

// Inclui o cabeçalho
include_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0">Cadastro</h2>
            </div>
            <div class="card-body">
                <form id="register-form" method="post" action="">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? sanitize($_POST['nome']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha">
                        <div class="form-text">A senha deve ter pelo menos 6 caracteres.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmar-senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirmar-senha" name="confirmar_senha">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Já tem uma conta? <a href="<?php echo BASE_URL; ?>/auth/login.php">Faça login</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé
include_once '../includes/footer.php';
?>