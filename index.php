<?php
/**
 * Página inicial do sistema
 * Exibe um slideshow e os filmes mais recentes
 */

// Inclui o arquivo de conexão com o banco de dados
require_once 'includes/db.php';

// Inclui o cabeçalho
include_once 'includes/header.php';

// Busca os filmes mais recentes
$filmes_recentes = fetch_all("SELECT f.*, GROUP_CONCAT(g.nome SEPARATOR ', ') as generos 
                            FROM filmes f 
                            LEFT JOIN filme_genero fg ON f.id = fg.filme_id 
                            LEFT JOIN generos g ON fg.genero_id = g.id 
                            GROUP BY f.id 
                            ORDER BY f.data_cadastro DESC 
                            LIMIT 6");
?>

<!-- Slideshow -->
<div class="slideshow-container mb-5">
    <div class="slideshow">
        <?php if (count($filmes_recentes) > 0): ?>
            <?php foreach ($filmes_recentes as $index => $filme): ?>
                <?php if ($index < 3): // Limita o slideshow a 3 filmes ?>
                    <div class="slideshow-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                        <img src="<?php echo BASE_URL; ?>/<?php echo $filme['imagem'] ?: 'assets/images/no-poster.jpg'; ?>" alt="<?php echo $filme['titulo']; ?>" class="slideshow-image">
                        <div class="slideshow-caption">
                            <h3><?php echo $filme['titulo']; ?></h3>
                            <p><?php echo substr($filme['descricao'], 0, 150); ?><?php echo (strlen($filme['descricao']) > 150) ? '...' : ''; ?></p>
                            <a href="<?php echo BASE_URL; ?>/filme.php?id=<?php echo $filme['id']; ?>" class="btn btn-primary">Ver Detalhes</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="slideshow-item active">
                <img src="<?php echo BASE_URL; ?>/assets/images/default-banner.jpg" alt="Bem-vindo ao Catálogo de Filmes" class="slideshow-image">
                <div class="slideshow-caption">
                    <h3>Bem-vindo ao Catálogo de Filmes</h3>
                    <p>Explore nossa coleção de filmes e descubra novas histórias.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Controles do slideshow -->
    <button class="slideshow-prev btn btn-dark position-absolute top-50 start-0 translate-middle-y ms-2" aria-label="Anterior">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="slideshow-next btn btn-dark position-absolute top-50 end-0 translate-middle-y me-2" aria-label="Próximo">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

<!-- Filmes Recentes -->
<section>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Filmes Recentes</h2>
        <a href="<?php echo BASE_URL; ?>/filmes.php" class="btn btn-outline-primary">Ver Todos</a>
    </div>
    
    <?php if (count($filmes_recentes) > 0): ?>
        <div class="filmes-grid">
            <?php foreach ($filmes_recentes as $filme): ?>
                <div class="card filme-card">
                    <img src="<?php echo BASE_URL; ?>/<?php echo $filme['imagem_thumb'] ?: 'assets/images/no-poster-thumb.jpg'; ?>" alt="<?php echo $filme['titulo']; ?>" class="filme-poster">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $filme['titulo']; ?></h5>
                        <p class="card-text small text-muted">
                            <?php if (!empty($filme['ano_lancamento'])): ?>
                                <span class="me-2"><i class="far fa-calendar-alt"></i> <?php echo $filme['ano_lancamento']; ?></span>
                            <?php endif; ?>
                            <?php if (!empty($filme['duracao'])): ?>
                                <span><i class="far fa-clock"></i> <?php echo $filme['duracao']; ?> min</span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($filme['generos'])): ?>
                            <div class="mb-2">
                                <?php foreach (explode(', ', $filme['generos']) as $genero): ?>
                                    <span class="badge-genero"><?php echo $genero; ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/filme.php?id=<?php echo $filme['id']; ?>" class="btn btn-sm btn-primary">Ver Detalhes</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p class="mb-0">Nenhum filme cadastrado ainda. <a href="<?php echo BASE_URL; ?>/admin/filmes/create.php">Adicionar filme</a></p>
        </div>
    <?php endif; ?>
</section>

<?php
// Inclui o rodapé
include_once 'includes/footer.php';
?>