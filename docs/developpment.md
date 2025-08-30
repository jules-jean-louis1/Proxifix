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

# BACKEND

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

## Qualité du Code

### Analyse Statique avec PHPStan

Pour vérifier que le code respecte les standards de qualité et de type safety, utilisez PHPStan :

```bash
vendor/bin/phpstan analyze
```

Cette commande analyse le code selon les règles définies dans `phpstan.neon` (niveau 6) et vérifie :

- La sécurité des types
- Les erreurs potentielles
- Le respect des bonnes pratiques

### Formatage du Code avec PHP-CS-Fixer

Pour vérifier le formatage du code selon les standards PSR-12 :

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
```

Pour corriger automatiquement les problèmes de formatage :

```bash
vendor/bin/php-cs-fixer fix
```

### Pipeline de Qualité Complète

Pour exécuter tous les contrôles de qualité en une seule fois :

```bash
# Tests unitaires
php bin/phpunit

# Analyse statique
vendor/bin/phpstan analyze

# Vérification du formatage
vendor/bin/php-cs-fixer fix --dry-run --diff

# Correction automatique du formatage
vendor/bin/php-cs-fixer fix
```

# FRONTEND

* [ ] Exécution des tests

```bash
npm run test
```

Lint le code

```bash
npm run lint
```

Formate le code

```bash
npm run format
```

Type-check le code

```bash
npm run type-check
```

Test complet pour CI

```bash
npm run ci
```

* [ ] Génération des Types TypeScript depuis le Backend

```bash
npm run generate-types
```

## Docker

```
./docker/docker.sh exec -e APP_ENV=test php php bin/console doctrine:database:create
./docker/docker.sh exec -e APP_ENV=test php php bin/console doctrine:migrations:migrate --no-interaction
./docker/docker.sh exec -e APP_ENV=test php php bin/console doctrine:fixtures:load --no-interaction

./docker/docker.sh exec -e APP_ENV=test php php bin/phpunit
```

# 🚀 Démarrage Rapide avec Docker

### Prérequis

- Docker et Docker Compose installés
- Port 81 (Nginx) et 5432 (PostgreSQL) disponibles

### Installation

```bash
# 1. Cloner le projet
git clone [url-du-repo]
cd proxifix_dev

# 2. Configurer l'environnement Docker
cp docker/.env.tpl docker/.env
# Modifier docker/.env si nécessaire (ports, mots de passe, etc.)

# 3. Démarrer les services
./docker/docker.sh up -d

# 4. Installer les dépendances du backend
./docker/docker.sh exec php composer install

# 5. Configurer la base de données
./docker/docker.sh exec php php bin/console doctrine:database:create --if-not-exists
./docker/docker.sh exec php php bin/console doctrine:migrations:migrate --no-interaction

# 6. Charger les données de test (optionnel)
./docker/docker.sh exec php php bin/console doctrine:fixtures:load --no-interaction

# 7. Installer les dépendances du mobile
cd mobile && npm install
```

### Services Docker

Le projet utilise Docker Compose avec les services suivants :

- **Nginx** (Port 81) - Serveur web
- **PHP-FPM** - Backend Symfony
- **PostgreSQL** (Port 5432) - Base de données
- **Traefik** - Reverse proxy (si activé)

### URLs après installation

- **API Backend** : http://localhost:81
- **Documentation API** : http://localhost:81/api/docs
- **PgAdmin** (si activé) : http://localhost:5050
- **Mobile App** : `cd mobile && npx expo start`

## 🐳 Commandes Docker Utiles

### Gestion des services

```bash
# Démarrer tous les services
./docker/docker.sh up -d

# Arrêter tous les services
./docker/docker.sh down

# Voir les logs
./docker/docker.sh logs -f

# Redémarrer un service spécifique
./docker/docker.sh restart php
```

### Commandes Symfony dans Docker

```bash
# Accéder au conteneur PHP
./docker/docker.sh exec php bash

# Migrations
./docker/docker.sh exec php php bin/console doctrine:migrations:migrate

# Fixtures (données de test)
./docker/docker.sh exec php php bin/console doctrine:fixtures:load

# Tests
./docker/docker.sh exec php php bin/phpunit

# Cache
./docker/docker.sh exec php php bin/console cache:clear
```

### Base de données

```bash
# Accéder à PostgreSQL
./docker/docker.sh exec db psql -U app -d cda_app

# Dump de la base
./docker/docker.sh exec db pg_dump -U app cda_app > backup.sql

# Restaurer une base
./docker/docker.sh exec -i db psql -U app -d cda_app < backup.sql
```

## 📋 Configuration Avancée

### Variables d'environnement (docker/.env)

```bash
# Ports
NGINX_PORT=81
DATABASE_PORT=5432

# Base de données
POSTGRES_DB=cda_app
POSTGRES_USER=app
POSTGRES_PASSWORD=password

# Projet
PROJECT_NAME=proxifix-dev
```

### SSL/HTTPS (Production)

Pour activer HTTPS en production, modifiez le fichier `docker/traefik.yml` et configurez vos certificats SSL.

## 🔧 Développement sans Docker

Si vous préférez développer sans Docker :

```bash
# Backend
cd backend
composer install
symfony server:start

# Mobile
cd mobile
npm install
npx expo start
```

## 🛠️ Stack Technique

### Backend (API)

- **PHP 8.1+** avec **Symfony 6**
- **PostgreSQL** pour la base de données
- **JWT Authentication** (Lexik Bundle)
- **API Platform** pour les APIs REST
- **PHPUnit** pour les tests

### Mobile (Frontend)

- **React Native** avec **Expo**
- **TypeScript**
- **React Hook Form** pour les formulaires
- **AsyncStorage** pour le cache local

## 📋 Fonctionnalités

### ✅ Gestion des Utilisateurs

- Authentification JWT multi-rôles
- ROLE_CUSTOMER / ROLE_TECHNICIAN / ROLE_ADMIN / ROLE_SUPER_ADMIN
- Gestion des entreprises et permissions

### ✅ Gestion des Interventions

- Création et assignation d'interventions
- Workflow complet : `pending` → `assigned` → `in_progress` → `completed`
- Gestion des équipements et types d'intervention
- Système de tâches associées aux interventions

### ✅ Planification

- Demandes de rendez-vous (AppointmentRequest)
- Gestion des créneaux libres
- Assignation automatique ou manuelle des techniciens

### 🚧 En Développement

- [ ] Application mobile complète
- [ ] Notifications push
- [ ] Rapports et statistiques
- [ ] Module de facturation

## 🏃‍♂️ Commandes Utiles

### Backend

```bash
cd backend

# Démarrer le serveur de développement
symfony server:start

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Lancer les tests
php bin/phpunit

# Charger des données de test
php bin/console doctrine:fixtures:load
```

### Mobile

```bash
cd mobile

# Démarrer l'app en développement
npx expo start

# Build pour production
npx expo build:android
npx expo build:ios
```

## 🌐 URLs de Développement

- **API Backend** : http://localhost:81
- **Documentation API** : http://localhost:81/api/docs
- **Mobile App** : `cd mobile && npx expo start` puis http://localhost:19006 (web)
- **Base de données** : localhost:5432 (PostgreSQL)

## 🔐 Comptes de Test

Après avoir chargé les fixtures avec `./docker/docker.sh exec php php bin/console doctrine:fixtures:load` :

```
Super Admin:
- Email: superadmin@mobile-cda.com
- Password: admin123

Admin Entreprise:
- Email: admin@entreprise.com
- Password: admin123

Customer:
- Email: customer@test.com
- Password: customer123

Technicien:
- Email: tech@entreprise.com
- Password: tech123
```

## 🚨 Dépannage

### Problèmes courants

```bash
# Ports déjà utilisés
sudo lsof -i :81  # Vérifier qui utilise le port 81
sudo lsof -i :5432  # Vérifier qui utilise le port 5432

# Problème de permissions
./docker/docker.sh exec php chown -R www-data:www-data /var/www/symfony/var

# Réinitialiser complètement
./docker/docker.sh down -v  # Supprimer les volumes
./docker/docker.sh up -d    # Redémarrer

# Voir les logs détaillés
./docker/docker.sh logs php  # Logs PHP
./docker/docker.sh logs db   # Logs PostgreSQL
```
