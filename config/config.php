<?php
// ------------------------------------------------------------------
// Este arquivo centraliza as variáveis de ambiente usadas na aplicação.
// A ideia é ter um único ponto de leitura de configs para facilitar o
// deploy em Vercel, Docker, Kubernetes ou execução local.
// ------------------------------------------------------------------

return [
    // Ambiente atual (apenas informativo, útil para logs ou ajustes futuros)
    'app_env' => getenv('APP_ENV') ?: 'local',

    // Configurações de banco de dados (MySQL)
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',          // Host do MySQL
        'port' => getenv('DB_PORT') ?: '3306',               // Porta do MySQL
        'name' => getenv('DB_NAME') ?: 'contatos_db',        // Nome do banco
        'user' => getenv('DB_USER') ?: 'crud_user',          // Usuário do banco
        'password' => getenv('DB_PASSWORD') ?: 'crud_password', // Senha do usuário
        'charset' => 'utf8mb4',                              // Charset para evitar problemas com acentuação
    ],
];

