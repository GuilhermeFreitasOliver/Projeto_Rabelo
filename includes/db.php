<?php
/**
 * Arquivo de conexão com o banco de dados
 * Estabelece a conexão com MySQL e fornece funções para operações no banco
 */

// Inclui o arquivo de configuração
require_once 'config.php';

/**
 * Função para obter conexão com o banco de dados
 * @return mysqli Objeto de conexão com o banco de dados
 */
function get_connection() {
    static $connection;

    if (!isset($connection)) {
        // Connect without specifying a database first
        $connection = new mysqli('localhost', 'root', '', '');
        
        // Check connection
        if ($connection->connect_error) {
            die('Connection failed: ' . $connection->connect_error);
        }
        
        // Create database if it doesn't exist
        $connection->query("CREATE DATABASE IF NOT EXISTS catalogo_filmes");
        
        // Select the database
        $connection->select_db('catalogo_filmes');
        
        // Set charset
        $connection->set_charset('utf8mb4');
    }

    return $connection;
}

/**
 * Função para executar consultas SQL
 * @param string $sql Query SQL a ser executada
 * @return mysqli_result|bool Resultado da consulta
 */
function query($sql) {
    $conn = get_connection();
    $result = $conn->query($sql);
    
    if (!$result && DEBUG) {
        die("Erro na consulta: " . $conn->error . "<br>SQL: " . $sql);
    }
    
    return $result;
}

/**
 * Função para obter um único registro do banco de dados
 * @param string $sql Query SQL a ser executada
 * @return array|null Array associativo com os dados ou null
 */
function fetch_assoc($sql) {
    $result = query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Função para obter múltiplos registros do banco de dados
 * @param string $sql Query SQL a ser executada
 * @return array Array de arrays associativos com os dados
 */
function fetch_all($sql) {
    $result = query($sql);
    $data = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

/**
 * Função para escapar strings para uso em consultas SQL
 * @param string $str String a ser escapada
 * @return string String escapada
 */
function escape($str) {
    $conn = get_connection();
    return $conn->real_escape_string($str);
}

/**
 * Função para obter o ID do último registro inserido
 * @return int ID do último registro inserido
 */
function last_insert_id() {
    $conn = get_connection();
    return $conn->insert_id;
}

/**
 * Função para inicializar o banco de dados (criar tabelas se não existirem)
 */
function init_database() {
    // The database is now created in get_connection()
    
    // Create tables
    query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Cria a tabela de usuários se não existir
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        papel ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    query($sql);
    
    // Cria a tabela de gêneros se não existir
    $sql = "CREATE TABLE IF NOT EXISTS generos (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nome VARCHAR(50) NOT NULL UNIQUE,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    query($sql);
    
    // Cria a tabela de filmes se não existir
    $sql = "CREATE TABLE IF NOT EXISTS filmes (
        id INT(11) NOT NULL AUTO_INCREMENT,
        titulo VARCHAR(100) NOT NULL,
        descricao TEXT,
        ano_lancamento YEAR,
        duracao INT,
        diretor VARCHAR(100),
        imagem VARCHAR(255),
        imagem_thumb VARCHAR(255),
        data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    query($sql);
    
    // Cria a tabela de avaliações se não existir
    $sql = "CREATE TABLE IF NOT EXISTS avaliacoes (
        id INT(11) NOT NULL AUTO_INCREMENT,
        filme_id INT(11) NOT NULL,
        usuario_id INT(11) NOT NULL,
        estrelas INT(1) NOT NULL,
        comentario TEXT,
        data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    query($sql);
    
    // Cria a tabela de relação entre filmes e gêneros
    $sql = "CREATE TABLE IF NOT EXISTS filme_genero (
        filme_id INT(11) NOT NULL,
        genero_id INT(11) NOT NULL,
        PRIMARY KEY (filme_id, genero_id),
        FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
        FOREIGN KEY (genero_id) REFERENCES generos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    query($sql);
    
    // Verifica se já existe um usuário administrador
    $admin = fetch_assoc("SELECT * FROM usuarios WHERE papel = 'admin' LIMIT 1");
    
    // Se não existir, cria um usuário administrador padrão
    if (!$admin) {
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha, papel) VALUES 
                ('Administrador', 'admin@exemplo.com', '$senha_hash', 'admin')";
        query($sql);
    }
}

/**
 * Busca um único registro no banco de dados
 * 
 * @param string $sql Query SQL a ser executada
 * @return array|null Array associativo com os dados ou null se não encontrar
 */
function fetch_one($sql) {
    $conn = get_connection();
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Inicializa o banco de dados
init_database();
?>