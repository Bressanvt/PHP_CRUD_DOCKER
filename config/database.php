<?php
// ------------------------------------------------------------------
// Responsável por criar e reaproveitar a conexão PDO com MySQL.
// Mantemos a função isolada para poder reutilizar em todas as páginas
// do CRUD e facilitar a troca de credenciais via variáveis de ambiente.
// ------------------------------------------------------------------

function getPdoConnection(): PDO
{
    // Carrega configurações definidas em config.php
    $config = require __DIR__ . '/config.php';
    $db = $config['db'];

    // DSN (Data Source Name) para MySQL
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['port'],
        $db['name'],
        $db['charset']
    );

    // Opções de PDO para segurança e performance
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,           // Lança exceções em erros
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Retorna arrays associativos
        PDO::ATTR_EMULATE_PREPARES => false,                   // Usa prepared statements nativos
    ];

    // Cria e retorna a instância PDO; em um projeto maior, poderíamos
    // implementar um singleton para reaproveitar a mesma conexão.
    return new PDO($dsn, $db['user'], $db['password'], $options);
}

// Exemplo de uso:
// $pdo = getPdoConnection();