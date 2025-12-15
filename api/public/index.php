<?php
// ------------------------------------------------------------------
// Ponto de entrada principal da aplicação.
// ------------------------------------------------------------------
// Integração com Vercel (comentários para estudo):
// 1) Crie um projeto no Vercel e conecte seu repositório GitHub.
// 2) A Vercel detecta o arquivo vercel.json na raiz e usa o builder
//    oficial de PHP (@vercel/php) para gerar o deploy serverless.
// 3) Cada push/commit em qualquer branch configurada dispara build
//    automático na Vercel, sem steps extras de CI local.
// 4) Para variáveis sensíveis (DB_HOST, DB_USER etc.), defina-as no
//    painel de Environment Variables da Vercel.
// ------------------------------------------------------------------

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/ContactRepository.php';

// Cria conexão PDO e injeta no repositório
$pdo = getPdoConnection();
$repository = new ContactRepository($pdo);

// Validação e criação de contato via POST (form abaixo)
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

    // Se passou na validação, cria o contato e recarrega a página
    if (empty($errors)) {
        $repository->create($nome, $email, $telefone);
        header('Location: index.php');
        exit;
    }
}

// Lista de contatos para renderização
$contacts = $repository->all();

// Função simples para escapar HTML e evitar XSS
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
    <title>CRUD de Contatos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">CRUD de Contatos (PHP + MySQL)</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Adicionar novo contato</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" required>
                </div>
                <button class="btn btn-primary" type="submit">Salvar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Lista de contatos</div>
        <div class="card-body">
            <?php if (empty($contacts)): ?>
                <p class="text-muted">Nenhum contato cadastrado ainda.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?= e($contact['id']) ?></td>
                                <td><?= e($contact['nome']) ?></td>
                                <td><?= e($contact['email']) ?></td>
                                <td><?= e($contact['telefone']) ?></td>
                                <td class="d-flex gap-2">
                                    <a class="btn btn-sm btn-warning" href="edit.php?id=<?= e($contact['id']) ?>">
                                        Editar
                                    </a>
                                    <a class="btn btn-sm btn-danger" href="delete.php?id=<?= e($contact['id']) ?>"
                                       onclick="return confirm('Deseja realmente excluir?');">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

