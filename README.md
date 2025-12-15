# PHP CRUD + Docker + Kubernetes + GitHub Actions

Aplicação simples em PHP puro com MySQL, pensada para estudo de deploy em Vercel, Docker/Docker Compose, Kubernetes, Docker Hub e CI no GitHub Actions.

## Estrutura de pastas
- `public/` — arquivos acessíveis pelo navegador (`index.php`, `edit.php`, `delete.php`).
- `config/` — configuração e conexão PDO (`config.php`, `database.php`).
- `app/` — repositório de dados (`ContactRepository.php`).
- `sql/` — script `schema.sql` para criar a tabela `contatos`.
- `k8s/` — manifests Kubernetes (Deployments, Services, ConfigMap, Secret, PVC).
- `.github/workflows/` — pipeline para build e push no Docker Hub.
- `Dockerfile` e `docker-compose.yml` — containerização local.
- `vercel.json` — instrução para Vercel usar o builder PHP.

## Preparação local
1) Instale Docker + Docker Compose.  
2) Copie o `env.example` para `.env` (opcionalmente) e ajuste credenciais:
   ```bash
   cp env.example .env
   ```
3) Suba os serviços:
   ```bash
   docker-compose up -d
   ```
4) Acesse `http://localhost:8080`. O MySQL fica exposto em `localhost:3307` (user `crud_user`, password `crud_password`, db `contatos_db`).

### Parar e limpar
```bash
docker-compose down        # para containers
docker-compose down -v     # remove volume e dados do MySQL
```

### Acesso externo ao MySQL
- **MySQL Workbench:** host `127.0.0.1`, porta `3307`, usuário `crud_user`, senha `crud_password`, database `contatos_db`.  
- **phpMyAdmin (opcional):** adicione ao `docker-compose.yml`:
  ```yaml
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports: ["8081:80"]
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
    depends_on: [mysql]
  ```
  Depois acesse `http://localhost:8081`.

## Vercel (deploy automático a cada commit)
1) Crie um repositório no GitHub e faça push do código.  
2) No dashboard da Vercel, clique em **Add New Project** e conecte ao repositório.  
3) A Vercel detectará o `vercel.json` e usará o builder PHP (`vercel-php@0.7.1`).  
4) Configure as variáveis de ambiente em **Settings > Environment Variables** (DB_HOST, DB_USER, DB_PASSWORD etc.).  
5) Cada push dispara um deploy automático. Para reverter, use o painel de deployments da Vercel.

## Docker Hub + GitHub Actions
- Workflow: `.github/workflows/docker-publish.yml`.
- Secrets necessários no GitHub: `DOCKER_USERNAME` e `DOCKER_PASSWORD`.
- A cada push o workflow:
  1) Faz checkout do repo.
  2) Faz login no Docker Hub.
  3) Faz build e push da imagem `DOCKER_USERNAME/php-crud-docker:latest` e tag com SHA curto.

## Kubernetes (exemplo genérico)
Arquivos em `k8s/`:
- `configmap.yaml` — DB host/porta/nome/usuário.
- `secret.yaml` — senhas em base64 (substitua pelos seus valores).
- `mysql-pvc.yaml` — volume para dados do MySQL.
- `mysql-deployment.yaml` + `mysql-service.yaml` — banco com 1 réplica e Service interno.
- `app-deployment.yaml` + `app-service.yaml` — aplicação com 3 réplicas e Service LoadBalancer (ou mude para NodePort).

Aplicar tudo (assumindo `kubectl` configurado):
```bash
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secret.yaml
kubectl apply -f k8s/mysql-pvc.yaml
kubectl apply -f k8s/mysql-deployment.yaml
kubectl apply -f k8s/mysql-service.yaml
kubectl apply -f k8s/app-deployment.yaml
kubectl apply -f k8s/app-service.yaml
```

### Acesso à aplicação no cluster
- Em nuvem: o Service `php-crud-service` (LoadBalancer) ganha um IP externo.  
- Em ambiente local (kind/minikube): use `minikube service php-crud-service` ou troque o tipo para `NodePort` e acesse pela porta exposta.

### Acesso externo ao MySQL no cluster
1) Troque `mysql-service.yaml` para `type: NodePort` e defina `nodePort: 30306`.  
2) Conecte via Workbench apontando para o IP do node e porta 30306.  
3) Usuário, senha e DB iguais aos definidos no ConfigMap/Secret.

## Notas rápidas de boas práticas
- Use secrets reais em produção (não commitar `.env`).  
- Configure limites de recursos em K8s e readiness/liveness probes.  
- Habilite logs centralizados e backups do MySQL.  
- Proteja a rota de exclusão com CSRF/autenticação em cenários reais.

