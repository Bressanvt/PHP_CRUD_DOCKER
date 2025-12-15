<?php
// ------------------------------------------------------------------
// Camada de acesso a dados (Repository) para contatos.
// Centraliza as operações CRUD usando PDO e prepared statements.
// ------------------------------------------------------------------

class ContactRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, nome, email, telefone FROM contatos ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, telefone FROM contatos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $contact = $stmt->fetch();

        return $contact ?: null;
    }

    public function create(string $nome, string $email, string $telefone): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO contatos (nome, email, telefone) VALUES (:nome, :email, :telefone)');
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
        ]);
    }

    public function update(int $id, string $nome, string $email, string $telefone): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE contatos SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM contatos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

