<?php
/**
 * Página de login
 * Permite que usuários façam login no sistema
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../includes/db.php';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $erro = false;
    
    // Verifica se os campos foram preenchidos
    if (empty($email) || empty($senha)) {
        $_SESSION['error'] = "Preencha todos os campos.";
        $erro = true;
    }
    
    if (!$erro) {
        // Busca o usuário pelo email
        $usuario = fetch_assoc("SELECT * FROM usuarios WHERE email = '" . escape($email) . "' LIMIT 1");
        
        // Verifica se o usuário existe e a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Inicia a sessão do usuário
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['user_role'] = $usuario['papel'];
            
            // Redireciona para a página inicial
            $_SESSION['success'] = "Login realizado com sucesso!";
            redirect(BASE_URL);
        } else {
            $_SESSION['error'] = "Email ou senha incorretos.";
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
                <h2 class="h4 mb-0">Login</h2>
            </div>
            <div class="card-body">
                <form id="login-form" method="post" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Não tem uma conta? <a href="<?php echo BASE_URL; ?>/auth/register.php" class="text-primary fw-bold">Cadastre-se</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé
include_once '../includes/footer.php';
?>