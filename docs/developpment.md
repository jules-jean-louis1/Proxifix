# Documentation de Développement

## 📚 Documentation Disponible

- **[business-logic.md](./business-logic.md)** - Logique métier et règles de gestion
- **[api-documentation.md](./api-documentation.md)** - Documentation complète des API
- **[test.md](./test.md)** - Guide des tests

## Installation et Configuration

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- PostgreSQL
- Node.js et npm (pour le frontend)

### Installation du Backend

Si vous utilisez docker, vous pouvez démarrer le projet avec la commande suivante :

```bash
docker compose up -d
```
Si vous n'utilisez pas Docker, suivez ces étapes :
1. Clonez le dépôt :
   ```bash
   git clone
   ```
2. Accédez au répertoire du projet :
   ```bash
   cd mobile-cda/backend
   ```
3. Installez les dépendances PHP :
   ```bash
   composer install
   ```
4. Configurez votre fichier `.env` :
   ```bash
   cp .env .env.local
   ```
   Modifiez les variables d'environnement selon votre configuration (base de données, clés API, etc.).
5. Créez la base de données :
   ```bash
   php bin/console doctrine:database:create
   ```
6. Exécutez les migrations :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
7. Chargez les fixtures (facultatif, pour peupler la base de données avec des données de test) :
   ```bash
   php bin/console doctrine:fixtures:load
   ```
8. Démarrez le serveur Symfony :
   ```bash
   symfony server:start
    ```
### Exécution des tests
Pour exécuter les tests, utilisez la commande suivante :

Configurez votre fichier `.env.test` pour les tests unitaires :
   ```bash
   cp .env.example .env.test
   ```
Modifiez les variables d'environnement selon votre configuration de test (base de données, clés API, etc.).

Assurez-vous que les dépendances de test sont installées :

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console doctrine:fixtures:load --env=test
```

```bash
php bin/phpunit
```
