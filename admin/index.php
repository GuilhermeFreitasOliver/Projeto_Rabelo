<?php
/**
 * Página principal da área administrativa
 * Exibe um dashboard com estatísticas e links para gerenciamento
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../includes/db.php';

// Verifica se o usuário é administrador
require_admin();

// Busca estatísticas para o dashboard
$total_filmes = fetch_assoc("SELECT COUNT(*) as total FROM filmes")['total'];
$total_generos = fetch_assoc("SELECT COUNT(*) as total FROM generos")['total'];
$total_usuarios = fetch_assoc("SELECT COUNT(*) as total FROM usuarios")['total'];
$filmes_recentes = fetch_all("SELECT * FROM filmes ORDER BY data_cadastro DESC LIMIT 5");

// Inclui o cabeçalho
include_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Painel Administrativo</h1>
</div>

<!-- Cards de estatísticas -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total de Filmes</h5>
                        <h2 class="mb-0"><?php echo $total_filmes; ?></h2>
                    </div>
                    <i class="fas fa-film fa-3x"></i>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/filmes/" class="text-white d-block mt-3">Gerenciar Filmes <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total de Gêneros</h5>
                        <h2 class="mb-0"><?php echo $total_generos; ?></h2>
                    </div>
                    <i class="fas fa-tags fa-3x"></i>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/generos/generos.php" class="text-white d-block mt-3">Gerenciar Gêneros <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total de Usuários</h5>
                        <h2 class="mb-0"><?php echo $total_usuarios; ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filmes recentes -->
<div class="card">
    <div class="card-header bg-light">
        <h2 class="h5 mb-0">Filmes Adicionados Recentemente</h2>
    </div>
    <div class="card-body">
        <?php if (count($filmes_recentes) > 0): ?>
            <div class="table-responsive">
                <table class="table table-admin table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Ano</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filmes_recentes as $filme): ?>
                            <tr>
                                <td><?php echo $filme['titulo']; ?></td>
                                <td><?php echo $filme['ano_lancamento'] ?: '-'; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($filme['data_cadastro'])); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/filme.php?id=<?php echo $filme['id']; ?>" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/filmes/update.php?id=<?php echo $filme['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/filmes/delete.php?id=<?php echo $filme['id']; ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este filme?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="mb-0">Nenhum filme cadastrado ainda.</p>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?php echo BASE_URL; ?>/admin/filmes/create.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Novo Filme
        </a>
    </div>
</div>

<?php
// Inclui o rodapé
include_once '../includes/footer.php';
?>