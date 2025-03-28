<?php
/**
 * Página de cadastro de filmes
 * Permite adicionar novos filmes ao catálogo
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../../includes/db.php';

// Verifica se o usuário é administrador
require_admin();

// Busca todos os gêneros para o formulário
$generos = fetch_all("SELECT * FROM generos ORDER BY nome ASC");

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitize($_POST['titulo']);
    $descricao = sanitize($_POST['descricao']);
    $ano_lancamento = !empty($_POST['ano_lancamento']) ? (int)$_POST['ano_lancamento'] : null;
    $duracao = !empty($_POST['duracao']) ? (int)$_POST['duracao'] : null;
    $diretor = sanitize($_POST['diretor']);
    $generos_selecionados = isset($_POST['generos']) ? $_POST['generos'] : [];
    $erro = false;
    
    // Verifica se o título foi preenchido
    if (empty($titulo)) {
        $_SESSION['error'] = "O título do filme é obrigatório.";
        $erro = true;
    }
    
    // Processa o upload da imagem
    $imagem = null;
    $imagem_thumb = null;
    
    if (!$erro && isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['imagem'];
        $nome_arquivo = $arquivo['name'];
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        
        // Verifica se a extensão é permitida
        if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Formato de imagem inválido. Use JPG, PNG ou GIF.";
            $erro = true;
        }
        
        // Verifica o tamanho do arquivo
        if (!$erro && $arquivo['size'] > MAX_FILE_SIZE) {
            $_SESSION['error'] = "A imagem deve ter no máximo 5MB.";
            $erro = true;
        }
        
        if (!$erro) {
            // Gera um nome único para o arquivo
            $nome_unico = uniqid() . '.' . $extensao;
            $caminho_imagem = 'assets/images/uploads/' . $nome_unico;
            $caminho_thumb = 'assets/images/uploads/thumb_' . $nome_unico;
            $caminho_completo = __DIR__ . '/../../' . $caminho_imagem;
            $caminho_thumb_completo = __DIR__ . '/../../' . $caminho_thumb;
            
            // Move o arquivo para o diretório de uploads
            if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
                $imagem = $caminho_imagem;
                
                // Cria a miniatura
                $img_original = null;
                
                if ($extensao === 'jpg' || $extensao === 'jpeg') {
                    $img_original = imagecreatefromjpeg($caminho_completo);
                } elseif ($extensao === 'png') {
                    $img_original = imagecreatefrompng($caminho_completo);
                } elseif ($extensao === 'gif') {
                    $img_original = imagecreatefromgif($caminho_completo);
                }
                
                if ($img_original) {
                    $largura_original = imagesx($img_original);
                    $altura_original = imagesy($img_original);
                    
                    // Calcula as dimensões da miniatura mantendo a proporção
                    $proporcao = $largura_original / $altura_original;
                    
                    if ($proporcao > 1) {
                        $largura_thumb = THUMB_WIDTH;
                        $altura_thumb = THUMB_WIDTH / $proporcao;
                    } else {
                        $altura_thumb = THUMB_HEIGHT;
                        $largura_thumb = THUMB_HEIGHT * $proporcao;
                    }
                    
                    // Cria a imagem redimensionada
                    $img_thumb = imagecreatetruecolor($largura_thumb, $altura_thumb);
                    
                    // Preserva transparência para PNG
                    if ($extensao === 'png') {
                        imagealphablending($img_thumb, false);
                        imagesavealpha($img_thumb, true);
                    }
                    
                    // Redimensiona a imagem
                    imagecopyresampled(
                        $img_thumb, $img_original,
                        0, 0, 0, 0,
                        $largura_thumb, $altura_thumb,
                        $largura_original, $altura_original
                    );
                    
                    // Salva a miniatura
                    if ($extensao === 'jpg' || $extensao === 'jpeg') {
                        imagejpeg($img_thumb, $caminho_thumb_completo, 90);
                    } elseif ($extensao === 'png') {
                        imagepng($img_thumb, $caminho_thumb_completo, 9);
                    } elseif ($extensao === 'gif') {
                        imagegif($img_thumb, $caminho_thumb_completo);
                    }
                    
                    // Libera a memória
                    imagedestroy($img_original);
                    imagedestroy($img_thumb);
                    
                    $imagem_thumb = $caminho_thumb;
                }
            } else {
                $_SESSION['error'] = "Erro ao fazer upload da imagem.";
                $erro = true;
            }
        }
    }
    
    // Insere o filme no banco de dados
    if (!$erro) {
        // Prepara os campos para a query
        $campos = [
            "titulo = '" . escape($titulo) . "'",
            "descricao = '" . escape($descricao) . "'"
        ];
        
        if ($ano_lancamento) {
            $campos[] = "ano_lancamento = $ano_lancamento";
        }
        
        if ($duracao) {
            $campos[] = "duracao = $duracao";
        }
        
        if (!empty($diretor)) {
            $campos[] = "diretor = '" . escape($diretor) . "'";
        }
        
        if ($imagem) {
            $campos[] = "imagem = '" . escape($imagem) . "'";
        }
        
        if ($imagem_thumb) {
            $campos[] = "imagem_thumb = '" . escape($imagem_thumb) . "'";
        }
        
        $sql = "INSERT INTO filmes SET " . implode(", ", $campos);
        
        if (query($sql)) {
            $filme_id = last_insert_id();
            
            // Associa os gêneros ao filme
            if (!empty($generos_selecionados)) {
                foreach ($generos_selecionados as $genero_id) {
                    $genero_id = (int)$genero_id;
                    query("INSERT INTO filme_genero (filme_id, genero_id) VALUES ($filme_id, $genero_id)");
                }
            }
            
            $_SESSION['success'] = "Filme cadastrado com sucesso!";
            redirect(BASE_URL . '/filme.php?id=' . $filme_id);
        } else {
            $_SESSION['error'] = "Erro ao cadastrar filme.";
        }
    }
}

// Inclui o cabeçalho
include_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Adicionar Filme</h1>
    <a href="<?php echo BASE_URL; ?>/admin/" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form id="filme-form" method="post" action="" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo isset($_POST['titulo']) ? sanitize($_POST['titulo']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="ano_lancamento" class="form-label">Ano de Lançamento</label>
                    <input type="number" class="form-control" id="ano_lancamento" name="ano_lancamento" min="1900" max="<?php echo date('Y') + 5; ?>" value="<?php echo isset($_POST['ano_lancamento']) ? (int)$_POST['ano_lancamento'] : ''; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="diretor" class="form-label">Diretor</label>
                    <input type="text" class="form-control" id="diretor" name="diretor" value="<?php echo isset($_POST['diretor']) ? sanitize($_POST['diretor']) : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="duracao" class="form-label">Duração (minutos)</label>
                    <input type="number" class="form-control" id="duracao" name="duracao" min="1" value="<?php echo isset($_POST['duracao']) ? (int)$_POST['duracao'] : ''; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="descricao" class="form-label">Sinopse</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="5"><?php echo isset($_POST['descricao']) ? sanitize($_POST['descricao']) : ''; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Gêneros</label>
                <div class="row">
                    <?php foreach ($generos as $genero): ?>
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="generos[]" value="<?php echo $genero['id']; ?>" id="genero-<?php echo $genero['id']; ?>" <?php echo (isset($_POST['generos']) && in_array($genero['id'], $_POST['generos'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="genero-<?php echo $genero['id']; ?>">
                                    <?php echo $genero['nome']; ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="imagem" class="form-label">Imagem do Cartaz</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg,image/png,image/gif">
                <div class="form-text">Formatos aceitos: JPG, PNG e GIF. Tamanho máximo: 5MB.</div>
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