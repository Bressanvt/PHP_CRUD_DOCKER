<?php
// Página de edição de contato.
// Objetivo: carregar o registro pelo ID, permitir atualizar e retornar para a lista.

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/ContactRepository.php';

$pdo = getPdoConnection();
$repository = new ContactRepository($pdo);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$contact = $id ? $repository->find($id) : null;

if (!$contact) {
    // Se o ID não existir, volta para a lista para evitar erro 404 personalizado
    header('Location: index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '') {
        $errors[] = 'Nome é obrigatório.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido.';
    }
    if ($telefone === '') {
        $errors[] = 'Telefone é obrigatório.';
    }

    if (empty($errors)) {
        $repository->update($id, $nome, $email, $telefone);
        header('Location: index.php');
        exit;
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar contato</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Editar contato #<?= e($contact['id']) ?></h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?= e($contact['nome']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?= e($contact['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="<?= e($contact['telefone']) ?>" required>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Salvar</button>
                    <a class="btn btn-secondary" href="index.php">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

