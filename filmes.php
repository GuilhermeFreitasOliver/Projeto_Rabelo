<?php
/**
 * Página de listagem de filmes
 * Exibe todos os filmes cadastrados no sistema
 */

// Inclui o arquivo de conexão com o banco de dados
require_once 'includes/db.php';

// Configuração de paginação
$itens_por_pagina = 12;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Filtro por gênero
$filtro_genero = isset($_GET['genero']) ? (int)$_GET['genero'] : 0;
$where_genero = $filtro_genero > 0 ? "WHERE fg.genero_id = $filtro_genero" : "";

// Busca os filmes com paginação
$filmes = fetch_all("SELECT f.*, GROUP_CONCAT(g.nome SEPARATOR ', ') as generos 
                    FROM filmes f 
                    LEFT JOIN filme_genero fg ON f.id = fg.filme_id 
                    LEFT JOIN generos g ON fg.genero_id = g.id 
                    $where_genero 
                    GROUP BY f.id 
                    ORDER BY f.titulo ASC 
                    LIMIT $offset, $itens_por_pagina");

// Conta o total de filmes para a paginação
$total_result = fetch_assoc("SELECT COUNT(DISTINCT f.id) as total 
                            FROM filmes f 
                            LEFT JOIN filme_genero fg ON f.id = fg.filme_id 
                            $where_genero");
$total_filmes = $total_result['total'];
$total_paginas = ceil($total_filmes / $itens_por_pagina);

// Busca todos os gêneros para o filtro
$generos = fetch_all("SELECT * FROM generos ORDER BY nome ASC");

// Inclui o cabeçalho
include_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Filmes</h1>
    
    <?php if (is_admin()): ?>
        <a href="<?php echo BASE_URL; ?>/admin/filmes/create.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Filme
        </a>
    <?php endif; ?>
</div>

<!-- Filtro por gênero -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="" class="row g-3">
            <div class="col-md-6">
                <label for="genero" class="form-label">Filtrar por Gênero</label>
                <select name="genero" id="genero" class="form-select">
                    <option value="0">Todos os gêneros</option>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?php echo $genero['id']; ?>" <?php echo ($filtro_genero == $genero['id']) ? 'selected' : ''; ?>>
                            <?php echo $genero['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <?php if ($filtro_genero > 0): ?>
                    <a href="<?php echo BASE_URL; ?>/filmes.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i> Limpar Filtro
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if (count($filmes) > 0): ?>
    <div class="filmes-grid">
        <?php foreach ($filmes as $filme): ?>
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
    
    <!-- Paginação -->
    <?php if ($total_paginas > 1): ?>
        <nav aria-label="Navegação de página" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($pagina_atual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/filmes.php?pagina=<?php echo $pagina_atual - 1; ?><?php echo $filtro_genero > 0 ? '&genero=' . $filtro_genero : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/filmes.php?pagina=<?php echo $i; ?><?php echo $filtro_genero > 0 ? '&genero=' . $filtro_genero : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($pagina_atual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/filmes.php?pagina=<?php echo $pagina_atual + 1; ?><?php echo $filtro_genero > 0 ? '&genero=' . $filtro_genero : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info">
        <p class="mb-0">Nenhum filme encontrado. <?php echo is_admin() ? '<a href="' . BASE_URL . '/admin/filmes/create.php">Adicionar filme</a>' : ''; ?></p>
    </div>
<?php endif; ?>

<?php
// Inclui o rodapé
include_once 'includes/footer.php';
?>