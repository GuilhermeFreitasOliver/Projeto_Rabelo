<?php
/**
 * Página de listagem de gêneros
 * Exibe todos os gêneros cadastrados no sistema
 */

// Inclui o arquivo de conexão com o banco de dados
require_once '../../includes/db.php';

// Verifica se o usuário é administrador
require_admin();

// Busca todos os gêneros
$generos = fetch_all("SELECT g.*, COUNT(fg.filme_id) as total_filmes 
                    FROM generos g 
                    LEFT JOIN filme_genero fg ON g.id = fg.genero_id 
                    GROUP BY g.id 
                    ORDER BY g.nome ASC");

// Inclui o cabeçalho
include_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gêneros</h1>
    <a href="<?php echo BASE_URL; ?>/admin/generos/create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Adicionar Gênero
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (count($generos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-admin table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Total de Filmes</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($generos as $genero): ?>
                            <tr>
                                <td><?php echo $genero['nome']; ?></td>
                                <td><?php echo $genero['total_filmes']; ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/filmes.php?genero=<?php echo $genero['id']; ?>" class="btn btn-sm btn-outline-primary" title="Ver Filmes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/generos/update.php?id=<?php echo $genero['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/generos/delete.php?id=<?php echo $genero['id']; ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este gênero?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="mb-0">Nenhum gênero cadastrado ainda.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Inclui o rodapé
include_once '../../includes/footer.php';
?>