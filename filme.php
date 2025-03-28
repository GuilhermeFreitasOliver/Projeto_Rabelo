<?php
/**
 * Página de detalhes do filme
 * Exibe informações detalhadas sobre um filme específico
 */

// Inclui o arquivo de conexão com o banco de dados
require_once 'includes/db.php';

// Verifica se o ID do filme foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID do filme inválido.";
    redirect(BASE_URL);
}

$id = (int) $_GET['id'];

// Busca os dados do filme
$filme = fetch_assoc("SELECT f.*, GROUP_CONCAT(g.nome SEPARATOR ', ') as generos 
                    FROM filmes f 
                    LEFT JOIN filme_genero fg ON f.id = fg.filme_id 
                    LEFT JOIN generos g ON fg.genero_id = g.id 
                    WHERE f.id = $id 
                    GROUP BY f.id");

// Verifica se o filme existe
if (!$filme) {
    $_SESSION['error'] = "Filme não encontrado.";
    redirect(BASE_URL);
}

// Inclui o cabeçalho
include_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <img src="<?php echo BASE_URL; ?>/<?php echo $filme['imagem'] ?: 'assets/images/no-poster.jpg'; ?>" alt="<?php echo $filme['titulo']; ?>" class="img-fluid rounded shadow">
    </div>
    
    <div class="col-md-8">
        <h1 class="mb-3"><?php echo $filme['titulo']; ?></h1>
        
        <div class="mb-3">
            <?php if (!empty($filme['generos'])): ?>
                <?php foreach (explode(', ', $filme['generos']) as $genero): ?>
                    <span class="badge-genero"><?php echo $genero; ?></span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="mb-4">
            <?php if (!empty($filme['ano_lancamento'])): ?>
                <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo $filme['ano_lancamento']; ?></span>
            <?php endif; ?>
            
            <?php if (!empty($filme['duracao'])): ?>
                <span class="me-3"><i class="far fa-clock"></i> <?php echo $filme['duracao']; ?> min</span>
            <?php endif; ?>
            
            <?php if (!empty($filme['diretor'])): ?>
                <span><i class="fas fa-film"></i> Diretor: <?php echo $filme['diretor']; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Sinopse</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($filme['descricao'])): ?>
                    <p><?php echo nl2br($filme['descricao']); ?></p>
                <?php else: ?>
                    <p class="text-muted">Nenhuma descrição disponível.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="d-flex">
            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            
            <?php if (is_admin()): ?>
                <a href="<?php echo BASE_URL; ?>/admin/filmes/update.php?id=<?php echo $filme['id']; ?>" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i> Editar
                </a>
                
                <a href="<?php echo BASE_URL; ?>/admin/filmes/delete.php?id=<?php echo $filme['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este filme?');">
                    <i class="fas fa-trash"></i> Excluir
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Seção de Avaliação Média -->
<div class="card mb-4">
    <div class="card-header">
        <h2 class="h5 mb-0">Avaliação</h2>
    </div>
    <div class="card-body">
        <?php
        // Busca a média de avaliações para este filme
        $avaliacao_media = fetch_assoc("SELECT AVG(estrelas) as media, COUNT(*) as total FROM avaliacoes WHERE filme_id = $id");
        $media = $avaliacao_media['media'] ? round($avaliacao_media['media'], 1) : 0;
        $total = $avaliacao_media['total'] ? $avaliacao_media['total'] : 0;
        ?>
        
        <div class="d-flex align-items-center mb-3">
            <div class="comentario-estrelas me-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?php echo ($i <= $media) ? 'active' : ''; ?>"></i>
                <?php endfor; ?>
            </div>
            <div>
                <strong><?php echo $media; ?></strong>/5 (<?php echo $total; ?> avaliações)
            </div>
        </div>
    </div>
</div>

<!-- Formulário de Avaliação -->
<?php if (is_logged_in()): ?>
    <?php
    // Verifica se o usuário já avaliou este filme
    $usuario_id = $_SESSION['user_id'];
    $avaliacao_existente = fetch_assoc("SELECT * FROM avaliacoes WHERE filme_id = $id AND usuario_id = $usuario_id");
    ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 mb-0"><?php echo $avaliacao_existente ? 'Editar sua avaliação' : 'Avaliar este filme'; ?></h2>
        </div>
        <div class="card-body">
            <form id="avaliacao-form" method="post" action="<?php echo BASE_URL; ?>/processar_avaliacao.php">
                <input type="hidden" name="filme_id" value="<?php echo $id; ?>">
                <input type="hidden" name="avaliacao_id" value="<?php echo $avaliacao_existente ? $avaliacao_existente['id'] : ''; ?>">
                
                <div class="mb-3">
                    <label class="form-label">Sua classificação:</label>
                    <div class="stars-container">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star star <?php echo ($avaliacao_existente && $i <= $avaliacao_existente['estrelas']) ? 'active' : ''; ?>" data-value="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="estrelas" id="estrelas" value="<?php echo $avaliacao_existente ? $avaliacao_existente['estrelas'] : '0'; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="comentario" class="form-label">Seu comentário:</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3"><?php echo $avaliacao_existente ? $avaliacao_existente['comentario'] : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo $avaliacao_existente ? 'Atualizar' : 'Enviar'; ?> avaliação</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <p class="mb-0">Para avaliar este filme, <a href="<?php echo BASE_URL; ?>/auth/login.php">faça login</a> ou <a href="<?php echo BASE_URL; ?>/auth/register.php">crie uma conta</a>.</p>
    </div>
<?php endif; ?>

<!-- Lista de Comentários -->
<div class="card mb-4">
    <div class="card-header">
        <h2 class="h5 mb-0">Comentários</h2>
    </div>
    <div class="card-body">
        <?php
        // Busca os comentários para este filme
        $comentarios = fetch_all("SELECT a.*, u.nome as nome_usuario 
                                FROM avaliacoes a 
                                JOIN usuarios u ON a.usuario_id = u.id 
                                WHERE a.filme_id = $id AND a.comentario != '' 
                                ORDER BY a.data_avaliacao DESC");
        
        if (count($comentarios) > 0):
            foreach ($comentarios as $comentario):
        ?>
            <div class="comentario border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong><?php echo $comentario['nome_usuario']; ?></strong>
                        <div class="comentario-estrelas">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo ($i <= $comentario['estrelas']) ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <span class="text-muted"><?php echo date('d/m/Y', strtotime($comentario['data_avaliacao'])); ?></span>
                </div>
                <p class="mb-0"><?php echo nl2br($comentario['comentario']); ?></p>
            </div>
        <?php
            endforeach;
        else:
        ?>
            <p class="text-muted mb-0">Nenhum comentário ainda. Seja o primeiro a avaliar!</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Inclui o rodapé
include_once 'includes/footer.php';
?>