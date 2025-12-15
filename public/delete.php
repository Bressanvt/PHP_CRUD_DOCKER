<?php
// Página simples para deletar contato.
// Mantemos separada para deixar o fluxo claro e fácil de explicar.

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/ContactRepository.php';

$pdo = getPdoConnection();
$repository = new ContactRepository($pdo);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $repository->delete($id);
}

// Redireciona de volta para a lista
header('Location: index.php');
exit;

